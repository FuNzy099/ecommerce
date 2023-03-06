<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends AbstractController
{
    /**
     *
     * @Route("/{slug}", name="product_category", priority=-1)
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
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
    
        $product = new Product;

        // Permet de créer un formulaire qui ce base sur la classe ProductType
        $form = $this -> createForm(ProductType::class, $product);

        // Permet d'analyser la requette
        $form->handleRequest($request);
        
        if($form -> isSubmitted() && $form -> isValid()){

            // On initialise le slug du produit en question
            $product -> setSlug(strtolower($slugger -> slug($product->getName())));

            $em -> persist($product);

            $em -> flush();

            // Permet de faire une redirection vers la route produc_show
            return $this -> redirectToRoute('product_show', [
            'category_slug' => $product -> getCategory() -> getSlug(),
            'slug' => $product -> getSlug()
            ]);

        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [

            'formView' => $formView

        ]);
    }



        /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator){

        $product = $productRepository -> find($id);

        $form = $this -> createForm(ProductType::class, $product);

        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form->isValid()) {
            
            $em -> flush();

            // Permet de faire une redirection vers la route produc_show
            return $this -> redirectToRoute('product_show', [
                'category_slug' => $product -> getCategory() -> getSlug(),
                'slug' => $product -> getSlug()
            ]);

        }

        $formView = $form -> createView();

        return $this->render('product/edit.html.twig', [

            'product' => $product,

            'formView' => $formView

        ]);

    }

    



}
