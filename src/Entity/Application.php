<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 */
class Application
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $reviews;

    /**
     * @ORM\Column(type="integer")
     */
    private $scoreText;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $icon;

    /**
     * @ORM\Column(type="integer")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $store;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ratings;

    /**
     * @ORM\Column(type="integer")
     */
    private $top;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="array")
     */
    private $key_words = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReviews(): ?int
    {
        return $this->reviews;
    }

    public function setReviews(int $reviews): self
    {
        $this->reviews = $reviews;

        return $this;
    }

    public function getScoreText(): ?int
    {
        return $this->scoreText;
    }

    public function setScoreText(int $scoreText): self
    {
        $this->scoreText = $scoreText;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStore(): ?string
    {
        return $this->store;
    }

    public function setStore(string $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getRatings(): ?string
    {
        return $this->ratings;
    }

    public function setRatings(string $ratings): self
    {
        $this->ratings = $ratings;

        return $this;
    }

    public function getTop(): ?int
    {
        return $this->top;
    }

    public function setTop(int $top): self
    {
        $this->top = $top;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getKeyWords(): ?array
    {
        return $this->key_words;
    }

    public function setKeyWords(array $key_words): self
    {
        $this->key_words = $key_words;

        return $this;
    }
}
