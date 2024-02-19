<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleEditType;
use App\Form\SellType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Stock;
use App\Entity\Invoice;
use App\Entity\Cart;
use App\Entity\User;
use App\Form\UserType;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/account/edit', name: 'app_account_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            $imageFile = $form->get('photo_profil')->getData();

            if ($imageFile) {
                $oldFilename = $user->getPhotoProfil();
                if ($oldFilename && file_exists($this->getParameter('images_directory') . '/' . $oldFilename)) {
                    unlink($this->getParameter('images_directory') . '/' . $oldFilename);
                }
            

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $user->setPhotoProfil($newFilename);
            
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            }
            if (!empty($plainPassword)) {
                $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/account/article_remove/{id}', name: 'app_account_remove_article')]
    public function removeArticle(EntityManagerInterface $entityManager, Article $article, Filesystem $filesystem): Response
    {
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
    
        if ($user !== $article->getIdAuteur()) {
            return $this->redirectToRoute('app_home');
        }
        

        $cartItems = $entityManager->getRepository(Cart::class)->findBy(['IdArticle' => $article]);
        foreach ($cartItems as $cartItem) {
            $entityManager->remove($cartItem);
        }
    
        $stocks = $entityManager->getRepository(Stock::class)->findBy(['Article_id' => $article]);
        foreach ($stocks as $stock) {
            $entityManager->remove($stock);
        }
        

        $imageFilename = $article->getLienImage();
        if ($imageFilename) {
        $imagePath = $this->getParameter('images_directory') . '/' . $imageFilename;
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }
    }
        

        $entityManager->remove($article);
    
        $entityManager->flush();
    
        return $this->redirectToRoute('app_account');
    }
    

    #[Route('/account/article_edit/{id}', name: 'app_account_edit_article')]
    public function editArticle(Request $request, EntityManagerInterface $entityManager, Article $article): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        
        if ($user !== $article->getIdAuteur()) {
            return $this->redirectToRoute('app_home');
        }
    
        $form = $this->createForm(ArticleEditType::class, $article);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
    
            $imageFile = $form->get('lien_image')->getData();
    
            if ($imageFile) {
                $oldFilename = $article->getLienImage();
                if ($oldFilename && file_exists($this->getParameter('images_directory') . '/' . $oldFilename)) {
                    unlink($this->getParameter('images_directory') . '/' . $oldFilename);
                }
            
    
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                $article->setLienImage($newFilename);
            
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            }
    
            $quantiteEnStock = $form->get('quantiteEnStock')->getData();
            $stock = $entityManager->getRepository(Stock::class)->findOneBy(['Article_id' => $article]);
    
            if ($stock) {
                $stock->setNombreArticleStock($quantiteEnStock);
            } else {
                $stock = new Stock();
                $stock->setArticleId($article);
                $stock->setNombreArticleStock($quantiteEnStock);
                $entityManager->persist($stock);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_account');
        }
    
        return $this->render('account/edit_article.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/account/user/{id}', name: 'user_profile')]
    public function showUserProfile(User $user, EntityManagerInterface $entityManager): Response
    {
        $articleRepository = $entityManager->getRepository(Article::class);
        $articles = $articleRepository->findBy(['Id_auteur' => $user], ['date_publication' => 'DESC']);

        return $this->render('account/user_profile.html.twig', [
            'user' => $user,
            'articles' => $articles
        ]);
    }

    



    #[Route('/account', name: 'app_account')]
    #[IsGranted("ROLE_USER")] 
    public function account(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $invoices = $user->getInvoices();

        $articles = $user->getArticles();

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'articles' => $articles,
            'invoices' => $invoices,
        ]);
    }
}
