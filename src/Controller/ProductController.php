<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     *
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {

        // Je récupere une catégorie à l'aide d'un critere avec la fonction findOneBy
        $category = $categoryRepository->findOneBy([

            'slug' => $slug // Permet de récuperer la catégorie correspondant au slug present dans L'URL

        ]);

        // Si il n'y a pas de catégorie correspondant au slug on affiche une erreur
        if (!$category) {

            /*
                throw permet de lancer une exception,
                createNotFounsException nous vient de la class AbstractController permettant d'afficher une erreur indiquant que la page n'existe pas (erreur 404)
            */
            throw $this->createNotFoundException("La catégorie demandée n'existe pas !");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }



    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority= -1)
     */
    public function show($slug, ProductRepository $productRepository)
    {

        // je récupere le prodruit à l'aide de la fonction findOneBy
        $product = $productRepository->findOneBy([

            'slug' => $slug // Permet de récuperer le produit correspondant au slug présent dans l'URL

        ]);

        // Si il n'y a pas de produit correspondant au slug on affiche une erreur
        if (!$product) {

            /*
                throw permet de lancer une exception,
                createNotFounsException nous vient de la class AbstractController permettant d'afficher une erreur indiquant que la page n'existe pas (erreur 404)
            */
            throw $this->createNotFoundException("Le produit demandée n'existe pas !");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,

        ]);
    }



    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger)
    {

        $builder = $factory->createBuilder(FormType::class, null, [
            'data_class' => Product::class
        ]);

        $builder

            ->add('name', TextType::class, [
                // Permet d'appliquer un label au champ input
                'label' => 'Nom du produit :',

                // Permet de gerer les attributs html du champ
                'attr' => [
                    // 'class' => 'form-control',                  // Permet d'appliquer une classe CSS venant de Bootstrap pour styliser le champ input
                    'placeholder' => 'Tapez le nom du produit'  // Permet d'appliquer une placeholder au sein même du input
                ]
            ])

            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte :',
                'attr' => [
                    // 'class' => 'form-control',
                    'placeholder' => 'Tapez une description suffisement courte mais parlante pour le visiteur'
                ]
            ])

            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit :',
                'attr' => [
                    // 'class' => 'form-control',
                    'placeholder' => 'Tapez le prix du produit en euro'
                ]
            ])

            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit :',
                'attr' => ['placeholder' => 'Tapez une URL d\'image !']
            ])

            ->add('category', EntityType::class, [
                'label' => 'Catégorie :',
                // 'attr' => [
                //     'class' => 'form-control'
                // ],
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => function(Category $category){
                    return strtoupper($category -> getName());
                },
            ]);
        
        $form = $builder->getForm();

        $form->handleRequest($request);
        
        if($form -> isSubmitted()){

            $product = $form->getData();
            $product -> setSlug(strtolower($slugger -> slug($product->getName())));

            dd($product);

        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [

            'formView' => $formView

        ]);
    }

    



}
