<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(operations: [new Post(), new Patch()])]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('feedback')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Feedback $feedback = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('feedback')]
    #[Assert\NotBlank]
    private ?string $body = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'replies')]
    private ?self $parentComment = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parentComment')]
    #[Groups('feedback')]
    private Collection $replies;

    #[ORM\Column(length: 50)]
    #[Groups('feedback')]
    #[Assert\NotBlank]
    private ?string $author = null;

    #[ORM\Column(length: 50)]
    #[Groups('feedback')]
    #[Assert\NotBlank]
    #[Assert\Regex('/^@[A-Za-z0-9._]+$/')]
    private ?string $authorHandle = null;

    #[ORM\Column]
    #[Groups('feedback')]
    private \DateTimeImmutable $publishedAt;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
        $this->replies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback): static
    {
        $this->feedback = $feedback;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getParentComment(): ?self
    {
        return $this->parentComment;
    }

    public function setParentComment(?self $parentComment): static
    {
        $this->parentComment = $parentComment;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(self $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->setParentComment($this);
        }

        return $this;
    }

    public function removeReply(self $reply): static
    {
        if ($this->replies->removeElement($reply)) {
            // set the owning side to null (unless already changed)
            if ($reply->getParentComment() === $this) {
                $reply->setParentComment(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthorHandle(): ?string
    {
        return $this->authorHandle;
    }

    public function setAuthorHandle(string $authorHandle): static
    {
        $this->authorHandle = $authorHandle;

        return $this;
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
