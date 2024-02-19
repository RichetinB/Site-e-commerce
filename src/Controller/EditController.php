<?php
// src/Controller/EditController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\Article;
use App\Form\ArticleEditType;

class EditController extends AbstractController
{
    #[Route('/account/edit/{id}', name: 'app_edit_article')]
    public function editArticle(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $article->getIdAuteur()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à modifier cet article.');
        }

        $form = $this->createForm(ArticleEditType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
