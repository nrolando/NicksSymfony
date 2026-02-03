<?php

namespace App\Controller;

use App\Services\Strings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/*
 * Created controller from the cli using Symfony recipe "symfony/maker-bundle"
 * composer require make
 * php bin/console make:controller
 */
class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        //return new Response('<h1>Welcome freeCodeCamp</h1>');

        // Instead of return a Response with html in the string (as above), we can use a twig template.
        // Initially (sym5.1), `composer require symfony/twig-bundle` added the root/template directory, and I created
        // the home/index.html.twig myself
        return $this->render('home/index.html.twig');
    }

    #[Route('/custom', name: 'custom')]
    public function custom(): Response
    {
        return new Response('<h1>Custom page!!</h1>');
    }

    #[Route('/coolpage/{name?}', name: 'coolpage')]
    public function coolpage(Request $request, Strings $stringsUtil): Response
    {
        // Get the Symfony dump method from recipe "symfony/var-dumper". May already come bundled with symfony/skeleton as of Symfony 5.
        // composer require dump
        //dump($request);

        $name = $request->get("name");
        $name = $stringsUtil->my_mb_ucwords($name);

        return $this->render('home/coolpage.html.twig', [
            'name'  => $name
        ]);
    }

    #[Route('/portfolio', name: 'portfolio_main')]
    public function portfolio(): Response
    {
        return $this->render('home/portfolio.html.twig');
    }
}
