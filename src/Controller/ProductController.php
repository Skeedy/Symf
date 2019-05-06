<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Products;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

            $entityManager = $this->getDoctrine()->getManager();
            $image = $product->getImage();
            $file = $form->get('image')->get('file')->getData();

            if ($file){

                $fileName = $this->generateUniqueFileName().'.'. $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('img_abs_path'), $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $image->setPath($this->getParameter('img_abs_path').'/'.$fileName) ;
                $image->setImgpath($this->getParameter('img_path').'/'.$fileName);
                $entityManager->persist($image);
            }else{
                $product->setImage(null);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $product->getImage();
            $file = $form->get('image')->get('file')->getData();

            if ($file){
                $fileName = $this->generateUniqueFileName().'.'. $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('img_abs_path'), $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $this->removeFile($image->getPath());
                $image->setPath($this->getParameter('img_abs_path').'/'.$fileName) ;
                $image->setImgpath($this->getParameter('img_path').'/'.$fileName);
                $entityManager->persist($image);
            }

            if (empty($image->getId()) && !$file ){
                $product->setImage(null);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_img_delete", methods={"POST"})
     */
    public function deleteImg(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $image = $product->getImage();
            $this->removeFile($image->getPath());

            $product->setImage(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->persist($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_edit', array('id'=>$product->getId()));
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $image = $product->getImage();
            $this->removeFile($image->getPath());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * @return string
     */
    function generateUniqueFileName() {

        return md5(uniqid());
    }

    private function removeFile($path){
        if(file_exists($path)){
            unlink($path);
        }
    }
}
