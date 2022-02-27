<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
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
        'security'=>'is_granted("ROLE_EDITOR") or (is_granted("ROLE_WRITER") and object.getAuthor() == user)'
        ]
        ],
        collectionOperations:['get','post'=>['security'=>'is_granted("ROLE_WRITER")']],
        denormalizationContext:['groups'=>'post']
)]
#[ApiFilter(SearchFilter::class,properties:['title'=>'partial','content'=>'partial','author'=>'exact','author.name'=>'partial'])]
#[ApiFilter(DateFilter::class,properties:['published'])]
#[ApiFilter(RangeFilter::class,properties:['id'])]
#[ApiFilter(OrderFilter::class,properties:['id','published','title'])]
#[ApiFilter(PropertyFilter::class,arguments:['parameterName'=>'properties','overrideDefaultProperties'=>false,'whitelist'=>['id','author','slug','title','content']])]
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

    /**
    * @ORM\ManyToMany(targetEntity=Image::class)
    * @ORM\JoinTable()
    */
    #[Groups(['get-blogpost-with-author','post'])]
    #[ApiSubresource()]
    private $images;

    public function __construct()
    {
        $this->setPublished(new DateTime());
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }
    
    public function __toString()
    {
        return  $this->title;
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

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }
}
