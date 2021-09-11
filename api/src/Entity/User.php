<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ApiResource(itemOperations:[
    'get'=>['security'=>"is_granted('IS_AUTHENTICATED_FULLY')",
    'normalization_context'=>["groups"=>['read']]],
    'put'=>['security'=>"is_granted('IS_AUTHENTICATED_FULLY') and object == user",
    'denormalization_context'=>["groups"=>['put']]]],
collectionOperations:['post'=>['denormalization_context'=>['groups'=>['post']]]])]
#[UniqueEntity(fields:"username")]
#[UniqueEntity(fields:"email")]
class User implements UserInterface,PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id; 

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read'])]
    #[Assert\NotBlank]
    #[Assert\Length(min:6,max:255)]
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Assert\Regex(pattern:"/(?=.*[A-Z])(?=.*[a-z]).{7,}/", message:"Password must be seven characters long and contain at least one digit, one uppercase letter")]
    private $password;

    #[Assert\NotBlank]
    #[Assert\Expression(expression:"this.getPassword()===this.getRetypedPassword()",message:"Passwords do not match")]
    private $retypedPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read','post','put'])]
    #[Assert\NotBlank]
    #[Assert\Length(min:6,max:255)]
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['post','put'])]
    #[Assert\NotBlank()]
    #[Assert\Length(min:6,max:255)]
    #[Assert\Email()]
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author", orphanRemoval=true)
     */
    #[Groups(['read'])]
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=BlogPost::class, mappedBy="author", orphanRemoval=true)
     */
    private $posts;

   
    #[ORM\Column(type:"json")]
    private $roles = [];

    // ...
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier()
    {
        return $this->username;
    }

    public function getSalt()
    {
        
    }

    public function eraseCredentials()
    {
        
    }

    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($password)
    {
        $this->retypedPassword =$password;
        return $this;
    }
}
