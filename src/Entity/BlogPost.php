<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BlogPostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 */
 #[ApiResource()]
                     class BlogPost
                     {
                         /**
                          * @ORM\Id
                          * @ORM\GeneratedValue
                          * @ORM\Column(type="integer")
                          */
                         private $id;
                     
                         /**
                          * @ORM\Column(type="string", length=255)
                          */
                         private $title;
                     
                         /**
                          * @ORM\Column(type="datetime")
                          */
                         private $published;
                     
                         /**
                          * @ORM\Column(type="text")
                          */
                         private $content;
                     
                         #[ORM\ManyToOne(targetEntity:User::class,inversedBy:"posts")]
                         #[ORM\JoinColumn(nullable:false)]
                         private $author;
                     
                         /**
                          * @ORM\Column(type="string", length=255, nullable=true)
                          */
                         private $slug;
            
                         /**
                          * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true)
                          */
                         private $comments;
                     
                         public function __construct()
                         {
                             $this->setPublished(new DateTime());
                             $this->comments = new ArrayCollection();
                         }
                     
                         public function getId(): ?int
                         {
                             return $this->id;
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
                     
                         public function getPublished(): ?\DateTimeInterface
                         {
                             return $this->published;
                         }
                     
                         public function setPublished(\DateTimeInterface $published): self
                         {
                             $this->published = $published;
                     
                             return $this;
                         }
                     
                         public function getContent(): ?string
                         {
                             return $this->content;
                         }
                     
                         public function setContent(string $content): self
                         {
                             $this->content = $content;
                     
                             return $this;
                         }
                     
                         public function getAuthor(): ?User
                         {
                             return $this->author;
                         }
                     
                         public function setAuthor(User $author): self
                         {
                             $this->author = $author;
                     
                             return $this;
                         }
                     
                         public function getSlug(): ?string
                         {
                             return $this->slug;
                         }
                     
                         public function setSlug(?string $slug): self
                         {
                             $this->slug = $slug;
                     
                             return $this;
                         }
      
                         /**
                          * @return Collection|Comment[]
                          */
                         public function getComments(): Collection
                         {
                             return $this->comments;
                         }
   
                         public function addComment(Comment $comment): self
                         {
                             if (!$this->comments->contains($comment)) {
                                 $this->comments[] = $comment;
                                 $comment->setPost($this);
                             }
   
                             return $this;
                         }

                         public function removeComment(Comment $comment): self
                         {
                             if ($this->comments->removeElement($comment)) {
                                 // set the owning side to null (unless already changed)
                                 if ($comment->getPost() === $this) {
                                     $comment->setPost(null);
                                 }
                             }

                             return $this;
                         }
                     }
