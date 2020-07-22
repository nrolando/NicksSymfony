<?php

namespace App\Controller;

use App\Services\Strings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/*
 * Created controller from the cli using Symfony recipe "symfony/maker-bundle"
 * composer require make
 * php bin/console make:controller
 */
class MainController extends AbstractController
{
    /** The below annotation (comment) is used by Symfony.
     * @Route("/", name="home")
     */
    public function index() {
        //return new Response('<h1>Welcome freeCodeCamp</h1>');

        // Instead of return a Response with html in the string (as above), we can use a twig template. This Twig
        // package was via "composer require symfony/twig-bundle". This added the root/template directory, and I created
        // the home/index.html.twig myself
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/custom", name="custom")
     */
    public function custom() {
        return new Response('<h1>Custom page!!</h1>');
    }

    /*
     * The {name?} in this route lets symfony know that this is a parameter in the url query string, and the "?"
     * denotes the parameter is optional.
     */
    /**
     * @Route("/coolpage/{name?}", name="coolpage")
     * @param Request $request
     * @param Strings $stringsUtil
     * @return Response
     */
    public function coolpage(Request $request, Strings $stringsUtil) {
        // Get the Symfony dump method from recipe "symfony/var-dumper". May already come bundled with symfony/skeleton as of Symfony 5.
        // composer require dump
        //dump($request);

        $name = $request->get("name");
        $name = $stringsUtil->my_mb_ucwords($name);

        return $this->render('home/coolpage.html.twig', [
            'name'  => $name
        ]);
    }

    /**
     * @Route("/portfolio", name="portfolio")
     */
    public function portfolio() {
        return $this->render('home/portfolio.html.twig');
    }
}
