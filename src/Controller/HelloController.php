<?php

namespace App\Controller;

use App\Taxes\Calculator;
use App\Taxes\Detector;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /*
        Explication de protected :
        Les propriétés, méthodes, ou constantes définies avec le mot clef protected ne vont être accessibles que depuis l'inteérieur de la classe qui les a definies
        ainsi que les classes qui en héritent ou la classe parente (principe d'héritage)
    */
    protected $logger;
    protected $calculator;

    /*
        J'ai crée un service* qui est la classe Calculator dont je fais la demande par injection de dépendance* à l'aide d'un constructeur (autowriring)
        Service : C'est un couche supplementaire dans nos Controller pour déléguer du travail que l'on ne veux pas faire dans nos controller mais plutôt dans des services
        Injection de dépendance : C'est un design pattern qui permet de solutionner la problématique de communication entre les classes
    */
    public function __construct(LoggerInterface $logger, Calculator $calculator)
    {
        $this->logger = $logger;
        $this->calculator = $calculator;
    }

    /**
     * @Route("/hello/{name?word}", name="hello") 
     */
    public function hello($name = "World")
    {

        return $this->render('hello.html.twig', [
            'name' => $name,
        ]);
    }
}
