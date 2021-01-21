<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomePageController extends AbstractController
{

    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', []);
    }
}
