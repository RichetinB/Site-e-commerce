<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\SellType;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\User;

class SellController extends AbstractController
{
    #[Route('/sell', name: 'app_sell')]
    #[IsGranted("ROLE_USER")] 
    public function sell(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $article = new Article();
        $form = $this->createForm(SellType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setDatePublication(new \DateTime());
            $article->setIdAuteur($this->getUser());

            $imageFile = $form->get('lien_image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $article->setLienImage($newFilename);

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            }

            $quantiteEnStock = $form->get('quantiteEnStock')->getData();

            try {
                $entityManager->persist($article);
                $entityManager->flush();

                $stock = new Stock();
                $stock->setArticleId($article);
                $stock->setNombreArticleStock($quantiteEnStock);
                $entityManager->persist($stock);
                $entityManager->flush();

                return $this->redirectToRoute('app_home');
            } catch (\Exception $e) {
                return $this->render('sell/index.html.twig', [
                    'form' => $form->createView(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->render('sell/index.html.twig', [
            'form' => $form->createView(),
            'error' => null,
        ]);
    }
}