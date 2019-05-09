<?php

namespace App\Controller;

use App\Entity\Allergen;
use App\Form\AllergenType;
use App\Repository\AllergenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/allergen")
 */
class AllergenController extends AbstractController
{
    /**
     * @Route("/", name="allergen_index", methods={"GET"})
     */
    public function index(AllergenRepository $allergenRepository): Response
    {
        return $this->render('allergen/index.html.twig', [
            'allergens' => $allergenRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="allergen_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $allergen = new Allergen();
        $form = $this->createForm(AllergenType::class, $allergen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

            $entityManager = $this->getDoctrine()->getManager();
            $image = $allergen->getImage();
            $file = $form->get('image')->get('file')->getData();

            if ($file){

                $fileName = $this->generateUniqueFileName().'.'. $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('img_allergen_abs_path'), $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $image->setPath($this->getParameter('img_allergen_abs_path').'/'.$fileName) ;
                $image->setImgpath($this->getParameter('img_allergen_path').'/'.$fileName);
                $entityManager->persist($image);
            }else{
                $allergen->setImage(null);
            }


            $entityManager->persist($allergen);
            $entityManager->flush();

            return $this->redirectToRoute('allergen_index');
        }

        return $this->render('allergen/new.html.twig', [
            'allergen' => $allergen,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="allergen_show", methods={"GET"})
     */
    public function show(Allergen $allergen): Response
    {
        return $this->render('allergen/show.html.twig', [
            'allergen' => $allergen,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="allergen_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Allergen $allergen): Response
    {
        $form = $this->createForm(AllergenType::class, $allergen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $allergen->getImage();
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
                $allergen->setImage(null);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('allergen_index', [
                'id' => $allergen->getId(),
            ]);
        }

        return $this->render('allergen/edit.html.twig', [
            'allergen' => $allergen,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="allergen_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Allergen $allergen): Response
    {
        if ($this->isCsrfTokenValid('delete'.$allergen->getId(), $request->request->get('_token'))) {
            $image = $allergen->getImage();
            if($image) {
                $this->removeFile($image->getPath());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($allergen);
            $entityManager->flush();
        }

        return $this->redirectToRoute('allergen_index');
    }
    function generateUniqueFileName() {

        return md5(uniqid());
    }

    private function removeFile($path){
        if(file_exists($path)){
            unlink($path);
        }
    }

}
