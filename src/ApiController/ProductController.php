<?php

namespace App\ApiController;

use App\Entity\Product;
use App\Form\ApiProductType;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Rest\Get(path="/{id}", name="Productshow_api")
     * @Rest\View()
     *
     * @param Product $product
     * @return View;
     */
    public function show(Product $product): View
    {
        return View::create($product, Response::HTTP_OK);
    }

    /**
     * create a product
     * @Rest\Post(
     *      path = "/new",
     *     name = "productcreate_api",
     * )
     * @param Request $request
     * @Rest\View()
     * @return View;
     */
    public  function create(Request $request): View
    {
        $product = new Product();
        $product->setName($request->get('name'));
        $product->setDescription($request->get('description'));
        $product->addAllergen($request->get('allergen'));
        $em = $this->getDoctrine()->getManager();
        $em ->persist($product);
        $em->flush();
        return View::create($product, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     *     path = "/{id}",
     *     name = "categoryedit_api",
     * )
     * @Rest\View()
     * @param Product $product
     * @return View;
     */
    public function edit(Request $request, Product $product): View
    {
        if($product){
            $product->setName($request->get('name'));
            $product->setDescription($request->get('description'));
            $product->addAllergen($request->get('allergen'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }
        return View::create($product,Response::HTTP_OK);
    }

    /**
     * @Rest\Patch(
     *     path = "/{id}",
     *     name = "productpatch_api",
     * )
     * @Rest\View()
     * @param Product $product
     * @return View;
     */
    public function patch(Request $request, Product $product): View
    {
        if($product){
            $form = $this->createForm(ApiProductType::class, $product);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }
        return View::create($product,Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path = "/{id}",
     *     name = "Productdelete_api",
     * )
     * @Rest\View()
     * @param Product $product
     * @return View;
     */
    public function delete(Product $product): View
    {
        if($product){
            $em = $this ->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }
        return View::create([], Response::HTTP_NO_CONTENT);
    }

}