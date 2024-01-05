<?php

namespace App\Entity;

use App\Repository\RefundsDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RefundsDetailsRepository::class)]
class RefundsDetails
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'refundsDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Refunds $refunds = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'refundsDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Products $products = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $price = null;

    public function getRefunds(): ?Refunds
    {
        return $this->refunds;
    }

    public function setRefunds(?Refunds $refunds): static
    {
        $this->refunds = $refunds;

        return $this;
    }

    public function getProducts(): ?Products
    {
        return $this->products;
    }

    public function setProducts(?Products $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }
}
