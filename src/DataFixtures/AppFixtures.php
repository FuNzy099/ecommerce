<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;


class AppFixtures extends Fixture
{

    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        
        $this -> slugger = $slugger;
        
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        // J'ajoute à faker des méthodes à l'aide de addProvider dans le but d'y ajouter une instance d'une extenssion.
        $faker -> addProvider(new \Liior\Faker\Prices($faker));
        $faker -> addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker -> addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        for($c = 0; $c < 3; $c++){

            // J'instancie un nouveau objet category
            $category = new Category;

            $category
                // Permet de définir le nom de la categorie
                -> setName($faker -> department)

                // Permet de définir le slug en ce basant sur le nom de la categorie, strtolower permet de mettre en minuscule tous les caracteres
                -> setSlug(strtolower($this -> slugger -> slug($category -> getName())));

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
                ->setSlug(strtolower($this -> slugger -> slug($product -> getName())))  

                // Permet de définir le produit à une categorie
                ->setCategory($category)

                // Permet de définir une description à un produit
                ->setShortDescription($faker -> paragraph())
                
                ->setMainPicture($faker->imageUrl(400, 400, true));

            // Persist permet de faire connaitre à l'ORM Doctrine l'existance de plusieurs produits
            $manager->persist($product);
        }
        }

   
        
        /*
            - Flush permet d'injecter dans la base de données les produits qui ont été persist.

            -Flush ne doit pas être dans la boucle for sinon la requette vas être exécuté 100 fois,
            Dans le but d'optimiser la requette et l'application je flush le produit en dehors de cette boucle.
        */ 
        $manager->flush();
    }
}
