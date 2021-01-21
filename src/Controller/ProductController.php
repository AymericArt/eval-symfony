<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{

    /**
     * @param Request $request
     * @param Product $product
     *
     * @return Response
     */
    public function viewAction(Request $request, Product $product): Response
    {
        return $this->render('default/product_view.html.twig', ['product' => $product]);
    }

    public function listAction(Request $request, EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('default/product_list.html.twig', ['products' => $products]);
    }
}
