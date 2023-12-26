<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/products', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    /**
     * Affiche l'identité de l'utilisateur
     *
     * @return Response
     */
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig',[
            'pageName' => 'Administration'
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/products/index.html.twig',[
            'pageName' => 'Administration - Add'
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Products $product): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_PRODUCT_ADMIN');
        //Appel du Voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        return $this->render('admin/products/index.html.twig',[
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


}