<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\Column(length: 40)]
    private ?string $surname = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Team $team = null;

    #[ORM\Column(nullable: true, options: ['default' => false])]
    private ?bool $IsAvailableForSell = null;

    #[ORM\OneToMany(mappedBy: 'player', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\Column(nullable: true)]
    private ?int $sellingPrice = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function isIsAvailableForSell(): ?bool
    {
        return $this->IsAvailableForSell;
    }

    public function setIsAvailableForSell(?bool $IsAvailableForSell): self
    {
        $this->IsAvailableForSell = $IsAvailableForSell;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setPlayer($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getPlayer() === $this) {
                $transaction->setPlayer(null);
            }
        }

        return $this;
    }

    public function getSellingPrice(): ?int
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(?int $sellingPrice): self
    {
        $this->sellingPrice = $sellingPrice;

        return $this;
    }
}
