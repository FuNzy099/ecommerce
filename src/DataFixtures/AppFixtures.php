<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{

    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $encoder)
    {
        
        $this -> slugger = $slugger;
        $this -> encoder = $encoder;
        
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        // J'ajoute à faker des méthodes à l'aide de addProvider dans le but d'y ajouter une instance d'une extenssion.
        $faker -> addProvider(new \Liior\Faker\Prices($faker));
        $faker -> addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker -> addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User;

        $hash = $this->encoder->hashPassword($admin, "password");

        $admin -> setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullName("Admin")
            ->setRoles(['ROLE_ADMIN']);

        $manager -> persist($admin);

        $users = [];




        // Boucle permettant de créer des utilisateur
        for($u = 0; $u < 5; $u++){

            $user = new User;

            $hash = $this->encoder->hashPassword($user, "password");

            $user -> setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);

            $users[] = $user;

            $manager -> persist($user);
        }


        $products = [];

        // Boucle permettant de créer des category
        for($c = 0; $c < 3; $c++){

            // J'instancie un nouveau objet category
            $category = new Category;

            $category
                // Permet de définir le nom de la categorie
                -> setName($faker -> department);

                // Permet de définir le slug en ce basant sur le nom de la categorie, strtolower permet de mettre en minuscule tous les caracteres
                // -> setSlug(strtolower($this -> slugger -> slug($category -> getName())));

            // Persist permet de faire connaitre à l'ORM Doctrine l'existance d'une categorie
            $manager -> persist($category);


            /*
                Boucle permettant de créer un certain nombre de produit pour une categorie

                mt_rand permet de génèrer une valeur aléatoire à l'occurance ici de 0 à un nombre entre 15 et 20

                DOCUMENTATION : 

                    mt_rand : https://www.php.net/manual/fr/function.mt-rand.php
            */ 
            for ($p = 0; $p < mt_rand(15, 20) ; $p++) {

            // J'instancie un nouveau objet product
            $product = new Product;

            $product
                // Permet de définir le nom du produit
                ->setName($faker->productName)     

                // Permet de définir le prix du produit, en utilisant la fonction mt_rand me permettant d'avoir un prix entre 100 et 200# pour ce cas de figure.
                ->setPrice($faker->price(4000, 20000)) 

                // Permet de définir le slug en ce basant sur le nom du produit, strtolower permet de mettre en minuscule tous les caracteres
                // ->setSlug(strtolower($this -> slugger -> slug($product -> getName())))  

                // Permet de définir le produit à une categorie
                ->setCategory($category)

                // Permet de définir une description à un produit
                ->setShortDescription($faker -> paragraph())
                
                ->setMainPicture($faker->imageUrl(400, 400, true));

            $products[] = $product;

            // Persist permet de faire connaitre à l'ORM Doctrine l'existance de plusieurs produits
            $manager->persist($product);
            }
        }

        for ($p=0; $p < mt_rand(20, 40); $p++) { 

            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months'));

            $selectedProduct = $faker->randomElements($products, mt_rand(3, 5));

            foreach ($selectedProduct as $product ) {
                $purchaseItem = new PurchaseItem;
                $purchaseItem->setProduct($product)
                    ->setQuantity(mt_rand(1,3))
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setTotal(
                        $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                    )
                    ->setPurchase($purchase);

                    $manager->persist($purchaseItem);
            }

            if($faker->boolean(90)){
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);

        }

   
        
        /*
            - Flush permet d'injecter dans la base de données les produits qui ont été persist.

            -Flush ne doit pas être dans la boucle for sinon la requette vas être exécuté 100 fois,
            Dans le but d'optimiser la requette et l'application je flush le produit en dehors de cette boucle.
        */ 
        $manager->flush();
    }
}
