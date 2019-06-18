<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeckCardRepository")
 */
class DeckCard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idCard;

    /**
     * @ORM\Column(type="integer", options={"default" : 1})
     */
    private $count;

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count): void
    {
        $this->count = $count;
    }

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Deck", inversedBy="deckCards")
     */
    private $deck;

    public function __construct()
    {
        $this->deck = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCard(): ?string
    {
        return $this->idCard;
    }

    public function setIdCard(string $idCard): self
    {
        $this->idCard = $idCard;

        return $this;
    }

    /**
     * @return Collection|Deck[]
     */
    public function getDeck(): Collection
    {
        return $this->deck;
    }

    public function addDeck(Deck $deck): self
    {
        if (!$this->deck->contains($deck)) {
            $this->deck[] = $deck;
        }

        return $this;
    }

    public function removeDeck(Deck $deck): self
    {
        if ($this->deck->contains($deck)) {
            $this->deck->removeElement($deck);
        }

        return $this;
    }
}
