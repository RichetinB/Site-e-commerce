<?php

// src/Controller/CartController.php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Invoice;
use App\Entity\Stock;
use App\Form\InvoiceType;



class CartController extends AbstractController
{

    #[Route('/cart/validate', name: 'app_cart_validate')]
    public function validate(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $cartItems = $user->getCarts();

        $totalAmount = 0;
        foreach ($cartItems as $cartItem) {
            $totalAmount += $cartItem->getIdArticle()->getPrix();
        }

        if ($totalAmount === 0) {
            return $this->redirectToRoute('app_home');
        }
        
        $solde  = $user->getSolde();

        
        if (!$user->hasEnoughBalance($totalAmount)) {
            return $this->render('cart/insufficient_balance.html.twig');
        }

        $invoice = new Invoice();
        
        $invoice->setDateTransaction(new \DateTime());
        $invoice->setUserId($this->getUser());
        $invoice->setMontant($totalAmount);

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user->setSolde($solde - $totalAmount);
            $entityManager->persist($invoice);
            $entityManager->flush();

            foreach ($cartItems as $cartItem) {
                $article = $cartItem->getIdArticle();
                $stock = $entityManager->getRepository(Stock::class)->findOneBy(['Article_id' => $article]);

                if ($stock instanceof Stock && $stock->getNombreArticleStock() > 0){
                    $stock->setNombreArticleStock($stock->getNombreArticleStock() - 1);
                    $entityManager->persist($stock);
                }
                
                $entityManager->remove($cartItem);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('cart/validate.html.twig', [
            'form' => $form->createView(),
            'totalAmount' => $totalAmount,
        ]);
    }



    #[Route('/cart/add/{id}', name: 'add_to_cart')]
public function addToCart(Request $request, EntityManagerInterface $entityManager, Article $article): Response
{
    $user = $this->getUser();

    if (!$user instanceof User) {
        return $this->redirectToRoute('app_login');
    }   

    $stock = $entityManager->getRepository(Stock::class)->findOneBy(['Article_id' => $article]);

    if ($stock instanceof Stock && $stock->getNombreArticleStock() > 0) {
        $cartItemsForArticle = $entityManager->getRepository(Cart::class)->findBy([
            'IdUser' => $user,
            'IdArticle' => $article
        ]);

        if (count($cartItemsForArticle) < $stock->getNombreArticleStock()) {
            $cartItem = new Cart();
            $cartItem->setIdUser($user);
            $cartItem->setIdArticle($article);

            $entityManager->persist($cartItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        } else {
            $this->addFlash('error', 'Vous avez atteint la quantité maximale d\'articles dans votre panier pour cet article.');
            return $this->redirectToRoute('app_detail', ['id' => $article->getId()]);

        }
    } else {
        $this->addFlash('error', 'Cet article n\'est plus en stock.');
        return $this->redirectToRoute('app_detail', ['id' => $article->getId()]);

    }
}



    #[Route('/cart/remove/{id}', name: 'remove_from_cart')]
    public function removeFromCart(EntityManagerInterface $entityManager, Cart $cartItem): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User || $cartItem->getIdUser() !== $user) {
            return $this->redirectToRoute('app_login');
        }

        $entityManager->remove($cartItem);
        $entityManager->flush();

        return $this->redirectToRoute('view_cart');
    }

    #[Route('/cart', name: 'view_cart')]
    public function viewCart(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les articles dans le panier de l'utilisateur
        $cartItems = $user->getCarts();

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
        ]);
    }
}
