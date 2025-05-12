<?php
// src/Entity/Comment.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\CommentRepository')]
class Comment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'smallint')]
    private int $rating;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Place $place = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // id
    public function getId(): int
    {
        return $this->id;
    }

    // content
    public function getContent(): string
    {
        return $this->content;
    }
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    // rating
    public function getRating(): int
    {
        return $this->rating;
    }
    public function setRating(int $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    // author
    public function getAuthor(): User
    {
        return $this->author;
    }
    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }

    // place
    public function getPlace(): ?Place
    {
        return $this->place;
    }
    public function setPlace(?Place $place): self
    {
        $this->place = $place;
        return $this;
    }

    // createdAt
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeInterface $dt): self
    {
        $this->createdAt = $dt;
        return $this;
    }
}
