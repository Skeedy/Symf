<?php

namespace App\ApiController;

use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/product", host="api.symfony.fr")
 */
class ProductController extends AbstractFOSRestController
{
    /**
     * @Route("/", name="Productlist_api", methods={"GET"})
     * @Rest\View()
     */
    public function index(ProductRepository $productRepository): View
    {
        $product = $productRepository->findAll();
        return View::create($product, Response::HTTP_OK);
    }
}