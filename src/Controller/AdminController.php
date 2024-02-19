<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Cart;
use App\Entity\Stock;
use Symfony\Component\Filesystem\Filesystem;
use App\Form\AdminArticleType;
use App\Form\AdminUserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;     



class AdminController extends AbstractController
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Security('is_granted(\'ROLE_ADMIN\')')] 
    #[Route('/admin', name: 'app_admin')]
    public function admin(
        AuthorizationCheckerInterface $authChecker,
        UserRepository $userRepository,
        ArticleRepository $articleRepository
    ): Response {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied.');
        }
        $users = $userRepository->findAll();
        $articles = $articleRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'articles' => $articles,
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'user_delete')]
public function deleteUser(User $user, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
{
    $articles = $entityManager->getRepository(Article::class)->findBy(['Id_auteur' => $user]);
    
    foreach ($articles as $article) {
        $this->deleteArticle($article, $entityManager, $filesystem);
    }

    $imageFilename = $user->getPhotoProfil();
        if ($imageFilename) {
        $imagePath = $this->getParameter('images_directory') . '/' . $imageFilename;
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }
    }

    $entityManager->remove($user);
    $entityManager->flush();

    $this->addFlash('success', 'User and associated articles deleted successfully.');

    return $this->redirectToRoute('app_admin');
}


    #[Route('/admin/article/{id}/delete', name: 'article_delete')]
    public function deleteArticle(Article $article, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
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

        $this->addFlash('success', 'Article deleted successfully.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/article/edit/{id}', name: 'app_admin_edit_article')]
    public function editArticle(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/edit_article.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'app_admin_edit_user')]
    public function editUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            if (!empty($plainPassword)) {
                $encodedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('admin/edit_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}