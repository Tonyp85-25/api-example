<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
#[ApiResource( 
    itemOperations:['get',
    'put'=>['security'=>"is_granted('IS_AUTHENTICATED_FULLY') and object.getAuthor() == user"]
    ],
    collectionOperations:['get',
    'post'=>['security'=>"is_granted('IS_AUTHENTICATED_FULLY')"],
    "api_blog_posts_comments_get_subresource"=>["normalization_context"=>['groups'=>'get-comment-with-author']]],
    denormalizationContext:['groups'=>'post']
    )]
class Comment implements AuthoredEntityInterface,PublishedEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['get-comment-with-author','get-blogpost-with-author'])]
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['post','get-comment-with-author', 'get-blogpost-with-author'])]
    #[Assert\NotBlank()]
    #[Assert\Length(min:5,max:3000)]
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['get-comment-with-author', 'get-blogpost-with-author'])]
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=BlogPost::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups('post')]
    private $post;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishedEntityInterface
    {
        $this->published = $published;

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

    public function getPost(): ?BlogPost
    {
        return $this->post;
    }

    public function setPost(?BlogPost $post): self
    {
        $this->post = $post;

        return $this;
    }
}
