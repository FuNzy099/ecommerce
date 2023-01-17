<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {

        var_dump("Ca fonctionne");
        die;
    }

    // La class Request vient de Symfony HTTPFoundation ! Cette classe contient un methode statique qui est createFromGlobals
    // La methode createFromGlobals prend en compte toutes les supper globals ($_POST, $_GET, $_SERVER, $_SESSION, etc etc)
    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET", "POST"}, schemes={"http", "https"})
     */
    public function test(Request $request)
    {

        // $age = à notre objet $request pour récupérer la propriété query (qui est le $_GET en PDO) pour choper notre information age
        $age = $request->attributes->get('age', 0);

        // Tous nos controller doivent retourner une réponse HTTP, c'est donc un objet de la classe Response
        return new Response("Vous avec $age ans!");
    }
}

// ! HttpFoundation : Requête et Réponse HTTP ! video 2 de la 4eme parties
