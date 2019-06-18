<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameCardRepository")
 */
class GameCard
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $object;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $card_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $oracle_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tcgplayer_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $printed_name;

    /**
     * @return mixed
     */
    public function getPrintedName()
    {
        return $this->printed_name;
    }

    /**
     * @param mixed $printed_name
     */
    public function setPrintedName($printed_name): void
    {
        $this->printed_name = $printed_name;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lang;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $small;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $png;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $normal;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $printed_text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $printed_type_line;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $released_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uri;

    /**
     * @return mixed
     */
    public function getSmall()
    {
        return $this->small;
    }

    /**
     * @param mixed $small
     */
    public function setSmall($small): void
    {
        $this->small = $small;
    }

    /**
     * @return mixed
     */
    public function getPng()
    {
        return $this->png;
    }

    /**
     * @param mixed $png
     */
    public function setPng($png): void
    {
        $this->png = $png;
    }

    /**
     * @return mixed
     */
    public function getNormal()
    {
        return $this->normal;
    }

    /**
     * @param mixed $normal
     */
    public function setNormal($normal): void
    {
        $this->normal = $normal;
    }

    /**
     * @return mixed
     */
    public function getPrintedText()
    {
        return $this->printed_text;
    }

    /**
     * @param mixed $printed_text
     */
    public function setPrintedText($printed_text): void
    {
        $this->printed_text = $printed_text;
    }

    /**
     * @return mixed
     */
    public function getPrintedTypeLine()
    {
        return $this->printed_type_line;
    }

    /**
     * @param mixed $printed_type_line
     */
    public function setPrintedTypeLine($printed_type_line): void
    {
        $this->printed_type_line = $printed_type_line;
    }

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $scryfall_uri;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $layout;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mana_cost;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type_line;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $oracle_text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $power;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $toughness;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $colors = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $color_identity = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $collector_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rarity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $illustration_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $artist;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $prices = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $purchase_uris = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="gameCards")
     */
    private $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getCardId(): ?string
    {
        return $this->card_id;
    }

    public function setCardId(string $card_id): self
    {
        $this->card_id = $card_id;

        return $this;
    }

    public function getOracleId(): ?string
    {
        return $this->oracle_id;
    }

    public function setOracleId(?string $oracle_id): self
    {
        $this->oracle_id = $oracle_id;

        return $this;
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

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setLang(?string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function getReleasedAt(): ?string
    {
        return $this->released_at;
    }

    public function setReleasedAt(string $released_at): self
    {
        $this->released_at = $released_at;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getScryfallUri()
    {
        return $this->scryfall_uri;
    }

    /**
     * @param mixed $scryfall_uri
     */
    public function setScryfallUri($scryfall_uri): void
    {
        $this->scryfall_uri = $scryfall_uri;
    }


    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function setLayout(?string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getManaCost()
    {
        return $this->mana_cost;
    }

    /**
     * @param mixed $mana_cost
     */
    public function setManaCost($mana_cost): void
    {
        $this->mana_cost = $mana_cost;
    }


    public function getTypeLine(): ?string
    {
        return $this->type_line;
    }

    public function setTypeLine(?string $type_line): self
    {
        $this->type_line = $type_line;

        return $this;
    }

    public function getOracleText(): ?string
    {
        return $this->oracle_text;
    }

    public function setOracleText(?string $oracle_text): self
    {
        $this->oracle_text = $oracle_text;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @param mixed $power
     */
    public function setPower($power): void
    {
        $this->power = $power;
    }

    /**
     * @return mixed
     */
    public function getTcgplayerId()
    {
        return $this->tcgplayer_id;
    }

    /**
     * @param mixed $tcgplayer_id
     */
    public function setTcgplayerId($tcgplayer_id): void
    {
        $this->tcgplayer_id = $tcgplayer_id;
    }

    /**
     * @return mixed
     */
    public function getToughness()
    {
        return $this->toughness;
    }

    /**
     * @param mixed $toughness
     */
    public function setToughness($toughness): void
    {
        $this->toughness = $toughness;
    }

    public function getColors(): ?array
    {
        return $this->colors;
    }

    public function setColors(?array $colors): self
    {
        $this->colors = $colors;

        return $this;
    }

    public function getColorIdentity(): ?array
    {
        return $this->color_identity;
    }

    public function setColorIdentity(?array $color_identity): self
    {
        $this->color_identity = $color_identity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCollectorNumber()
    {
        return $this->collector_number;
    }

    /**
     * @param mixed $collector_number
     */
    public function setCollectorNumber($collector_number): void
    {
        $this->collector_number = $collector_number;
    }



    public function getRarity(): ?string
    {
        return $this->rarity;
    }

    public function setRarity(?string $rarity): self
    {
        $this->rarity = $rarity;

        return $this;
    }

    public function getIllustrationId(): ?string
    {
        return $this->illustration_id;
    }

    public function setIllustrationId(?string $illustration_id): self
    {
        $this->illustration_id = $illustration_id;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(?string $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getPrices(): ?array
    {
        return $this->prices;
    }

    public function setPrices(?array $prices): self
    {
        $this->prices = $prices;

        return $this;
    }

    public function getPurchaseUris(): ?array
    {
        return $this->purchase_uris;
    }

    public function setPurchaseUris(?array $purchase_uris): self
    {
        $this->purchase_uris = $purchase_uris;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
        }

        return $this;
    }
}
