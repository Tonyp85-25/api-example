<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\BlogPostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 */
 #[ApiResource(
        itemOperations:[
       'get'=>['normalization_context' => ['groups'=> 'get-blogpost-with-author']],
        'put'=>[
            'security'=>'is_granted("IS_AUTHENTICATED_FULLY") and object.getAuthor() == user'
            ]
           ],
           collectionOperations:['get','post'=>['security'=>'is_granted("IS_AUTHENTICATED_FULLY")']],
           denormalizationContext:['groups'=>'post']
        )]
   class BlogPost implements AuthoredEntityInterface, PublishedEntityInterface
   {
       /**
        * @ORM\Id
       * @ORM\GeneratedValue
       * @ORM\Column(type="integer")
       */
       #[Groups(['get-blogpost-with-author'])]
       private $id;
   
       /**
        * @ORM\Column(type="string", length=255)
       */
       #[Assert\NotBlank()]
       #[Groups(['post','get-blogpost-with-author'])]
       private $title;
   
       /**
        * @ORM\Column(type="datetime")
       */
       #[Assert\NotBlank()]
       #[Assert\DateTime()]
       #[Groups(['get-blogpost-with-author'])]
       private $published;
   
       /**
        * @ORM\Column(type="text")
       */
       #[Assert\Length(min:20)]
       #[Groups(['post','get-blogpost-with-author'])]
       private $content;
   
   
       /**
        * @ORM\Column(type="string", length=255, nullable=true)
       */
       #[Assert\NotBlank()]
       #[Groups(['post','get-blogpost-with-author'])]
       private $slug;
   
       /**
        * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true)
       */
       #[ApiSubresource()]
       #[Groups(['get-blogpost-with-author'])]
       private $comments;

       /**
        * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogPosts")
        * @ORM\JoinColumn(nullable=false)
        */
        #[Groups(['get-blogpost-with-author'])]
       private $author;
   
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
   
       public function setPublished(\DateTimeInterface $published): PublishedEntityInterface
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
   
       public function setAuthor(UserInterface $author): AuthoredEntityInterface
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
