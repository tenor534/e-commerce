<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders', name: 'app_orders_')]
class OrdersController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(SessionInterface $session, ProductsRepository $productsRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $cart = $session->get('cart', []);

        if($cart === []){
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('app_main');
        }

        //Le panier n'est pas vide, on crée la commande
        $order = new Orders();

        // On remplit la commande
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());

        // On parcourt le panier pour créer les détails de commande
        foreach($cart as $item => $quantity){
            $orderDetails = new OrdersDetails();

            // On va chercher le produit
            $product = $productsRepository->find($item);
            
            $price = $product->getPrice();

            // On crée le détail de commande
            $orderDetails->setProducts($product);
            $orderDetails->setPrice($price);
            $orderDetails->setQuantity($quantity);

            $order->addOrdersDetail($orderDetails);
        }

        // On persiste et on flush
        $em->persist($order);
        $em->flush();

        $session->remove('cart');

        $this->addFlash('message', 'Commande créée avec succès');
        return $this->redirectToRoute('app_main');
    }
}
