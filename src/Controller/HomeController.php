<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository)
    {

        // Permet de récuperer 3 produits sans critere en particulier provenant de la base de données.
        $products = $productRepository -> findBy([], [], 3);

        return $this -> render('home.html.twig', [

            'products' => $products

        ]);

    }

}