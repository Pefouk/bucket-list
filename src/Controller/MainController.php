<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * Affiche la page d'accueil
     * @Route("", name="home")
     */
    public function home() {
        return $this->render("main/home.html.twig");
    }

    /**
     * Affiche un about us
     * @Route("/aboutus", name="aboutus")
     */
    public function aboutUs() {
        return $this->render("main/aboutus.html.twig");
    }
}