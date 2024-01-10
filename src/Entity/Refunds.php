<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\RefundsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RefundsRepository::class)]
class Refunds
{
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $refunds_type = null;

    #[ORM\Column(length: 150)]
    private ?string $reference = null;   

    #[ORM\ManyToOne(inversedBy: 'refunds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Orders $orders = null;

    #[ORM\OneToMany(mappedBy: 'refunds', targetEntity: RefundsDetails::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $refundsDetails;

    public function __construct()
    {
        $this->refundsDetails = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefundsType(): ?int
    {
        return $this->refunds_type;
    }

    public function setRefundsType(int $refunds_type): static
    {
        $this->refunds_type = $refunds_type;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
   
    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(?Orders $orders): static
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * @return Collection<int, RefundsDetails>
     */
    public function getRefundsDetails(): Collection
    {
        return $this->refundsDetails;
    }

    public function addRefundsDetail(RefundsDetails $refundsDetail): static
    {
        if (!$this->refundsDetails->contains($refundsDetail)) {
            $this->refundsDetails->add($refundsDetail);
            $refundsDetail->setRefunds($this);
        }

        return $this;
    }

    public function removeRefundsDetail(RefundsDetails $refundsDetail): static
    {
        if ($this->refundsDetails->removeElement($refundsDetail)) {
            // set the owning side to null (unless already changed)
            if ($refundsDetail->getRefunds() === $this) {
                $refundsDetail->setRefunds(null);
            }
        }

        return $this;
    }
}
