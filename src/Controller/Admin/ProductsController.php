<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Service\FileUploaderService;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Repository\ProductsRepository;
use App\Service\PicturesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/products', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    /**
     * Affiche l'identité de l'utilisateur
     *
     * @return Response
     */
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $productsRipository): Response
    {
        $products = $productsRipository->findBy(
            [], 
            ['id' => 'asc']
        );
        return $this->render('admin/products/index.html.twig',[
            'products' => $products,
            'pageName' => 'Administration'
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(
        Request $request, 
        EntityManagerInterface $entityManager, 
        SluggerInterface $slugger, 
        FileUploaderService $fileUploader,
        PicturesService $pictureService
        ): Response
    {       
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Products();

        $product->setBrochureFilename('');

        $form = $this->createForm(ProductsFormType::class, $product);
        
        //Traitement des requêtes: formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Generation du slug
            $slug = $slugger->slug($product->getName())->lower();
            $product->setSlug($slug);
            
            //$price =$product->getPrice() * 100;
            //$product->setPrice($price);


            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setBrochureFilename($brochureFileName);
            }

            //On récupère les images
            $images = $form->get('images')->getData();
            
            foreach($images as $image){
                //dossier de destination
                $folder = ''; //Personalisable : 'products/dossier/ ...'
               
                //Service d'ajout
                $ficher = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($ficher);
                $product->addImage($img);
            }            

            $entityManager->persist($product);
            $entityManager->flush();

            //Affiche un message flash
            $this->addFlash('success', 'Product added');

            return $this->redirectToRoute('admin_products_index');

        }

        return $this->render('admin/products/add.html.twig',[
            'productForm' => $form->createView(),
            'product' => $product, 
            'pageName' => 'Administration - Add'
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(
        Products $product, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        SluggerInterface $slugger, 
        FileUploaderService $fileUploader,
        PicturesService $pictureService
        ): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_PRODUCT_ADMIN');
        //Appel du Voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
     
        //Modify the price
        //$product->setPrice($product->getPrice() / 100);
       
        /*if($product->getBrochureFilename()){
            $product->setBrochureFilename(
                new File($this->getParameter('brochures_directory').'/'.$product->getBrochureFilename())
            );
        }*/

        $form = $this->createForm(ProductsFormType::class, $product);

       //Traitement des requêtes: formulaire
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           // Generation du slug
           $product->setSlug($slugger->slug($product->getName())->lower());           
           // Price           
           //$product->setPrice($product->getPrice() * 100);

           /** @var UploadedFile $brochureFile */
           $brochureFile = $form->get('brochure')->getData();
           // this condition is needed because the 'brochure' field is not required
           // so the PDF file must be processed only when a file is uploaded
           if ($brochureFile) {
               $brochureFileName = $fileUploader->upload($brochureFile);
               // updates the 'brochureFilename' property to store the PDF file name
               // instead of its contents
               $product->setBrochureFilename($brochureFileName);
           }

           //On récupère les images
           $images = $form->get('images')->getData();
            
           foreach($images as $image){
               //dossier de destination
               $folder = ''; //Personalisable : 'products/dossier/ ...'
              
               //Service d'ajout
               $ficher = $pictureService->add($image, $folder, 300, 300);

               $img = new Images();
               $img->setName($ficher);
               $product->addImage($img);
           }              

           $entityManager->persist($product);
           $entityManager->flush();

           //Affiche un message flash
           $this->addFlash('success', 'Product updated');

           return $this->redirectToRoute('admin_products_index');
       }

        return $this->render('admin/products/edit.html.twig',[
            'productForm' => $form->createView(),
            'product' => $product,            
            'pageName' => 'Administration - Edit'
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Products $product): Response
    {
         //Appel du Voter
         $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        return $this->render('admin/products/index.html.twig',[
            'pageName' => 'Administration - Delete'
        ]);
    }

    #[Route('/delete/image/{id}', name: 'delete_image', methods:['DELETE'])]
    public function deleteImage(
        Images $image,
        Request $request,
        EntityManagerInterface $entityManager,
        PicturesService $pictureService
    ): JsonResponse
    {
         //Appel du Voter
         //$this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);      

         //On récupère le contenu de la requete
         $data = json_decode($request->getContent(), true);           

         if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])){
            //le token csrf est valide
            //On récupère l'image
            $nom = $image->getName();

            //Suppression physique
            if($pictureService->delete($nom, '', 300, 300)){
                // Suppression en base de données
                $entityManager->remove($image);
                $entityManager->flush();              

                return new JsonResponse(['success' => true], 200);
            }

            return new JsonResponse(['error' => 'Erreur de suppression'], 400);
        }             
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}