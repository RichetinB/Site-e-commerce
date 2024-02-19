<?php

// src/Controller/HomeController.php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Stock;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;  
use Symfony\Component\HttpFoundation\Request;  
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]

    public function index(EntityManagerInterface $entityManager): Response
    {
            $articleRepository = $entityManager->getRepository(Article::class);
            $articles = $articleRepository->findBy([],['date_publication' => 'DESC']);
            
            $userRepository = $entityManager->getRepository(User::class);
            $users = $userRepository->findAll();
            return $this->render('home/index.html.twig', [
                'articles' => $articles,
                'users' => $users
            ]);
        
        
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $query = $request->query->get('query');
        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();
        $articles = $entityManager->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->where('a.nom LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        return $this->render('home/search_results.html.twig', [
            'query' => $query,
            'articles' => $articles,
            'users' => $users
        ]);
    }



}