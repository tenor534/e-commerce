<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use SessionIdInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'app_cart_')]
class CartController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository): Response
    {

        //Panier existant : 
        $cart = $session->get('cart', []);

       
        $data = [];
        $total = 0;    

        foreach($cart as $id => $quantite){
            $product = $productsRepository->find($id);
            $data[] = [
                'product' => $product,
                'quantity' => $quantite
            ];
            
            $total += $product->getPrice() * $quantite;            
        }      
        
        return $this->render('cart/index.html.twig', [
            'controller_name'   => 'CartController',
            'data'              => $data,
            'total'             => $total,
            'pageName'          => 'Ajout panier'
        ]);
    }

    #[Route('/add/{id}', name: 'add')]
    public function add(Products $products, SessionInterface $session)
    {

        //Id du produit : 
        $id = $products->getId();

        //Panier existant : 
        $cart = $session->get('cart', []);

        //Ajout du produit dans le panier, s'il existe pas encore, sinon on incrémente la quantité
        if(empty($cart[$id])){
            $cart[$id] = 1;
        }else{
            $cart[$id]++;
        }

        $session->set('cart', $cart);      
        
        //Redirection vers la page index

        return $this->redirectToRoute('app_cart_index');
    }


    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Products $product, SessionInterface $session)
    {
        //On récupère l'id du produit
        $id = $product->getId();

        // On récupère le panier existant
        $cart = $session->get('cart', []);

        // On retire le produit du panier s'il n'y a qu'1 exemplaire
        // Sinon on décrémente sa quantité
        if(!empty($cart[$id])){
            if($cart[$id] > 1){
                $cart[$id]--;
            }else{
                unset($cart[$id]);
            }
        }

        $session->set('cart', $cart);
        
        //On redirige vers la page du panier
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Products $product, SessionInterface $session)
    {
        //On récupère l'id du produit
        $id = $product->getId();

        // On récupère le panier existant
        $cart = $session->get('cart', []);

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
        
        //On redirige vers la page du panier
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/empty', name: 'empty')]
    public function empty(SessionInterface $session)
    {
        $session->remove('cart');

        return $this->redirectToRoute('app_cart_index');
    }
}
