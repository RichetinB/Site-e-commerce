<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $Article_id = null;

    #[ORM\Column]
    private ?int $nombre_article_stock = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleId(): ?Article
    {
        return $this->Article_id;
    }

    public function setArticleId(?Article $Article_id): static
    {
        $this->Article_id = $Article_id;

        return $this;
    }

    public function getNombreArticleStock(): ?int
    {
        return $this->nombre_article_stock;
    }

    public function setNombreArticleStock(int $nombre_article_stock): static
    {
        $this->nombre_article_stock = $nombre_article_stock;

        return $this;
    }
}
