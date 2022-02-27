<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ResetPasswordAction;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ApiResource(
    itemOperations: [
        'get' => [
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
            'normalization_context' => ["groups" => ['get']]
        ],
        'put' => [
            'security' => "is_granted('IS_AUTHENTICATED_FULLY') and object == user",
            'denormalization_context' => ['groups' => 'put'],
            'normalization_context' => ["groups" => ['get']]
        ],
        'put-reset-password' => [
            'security' => "is_granted('IS_AUTHENTICATED_FULLY') and object == user",
            'denormalization_context' => ['groups' => 'put-reset-password'],
            'method' => 'PUT',
            'path' => '/users/{id}/reset-password',
            'controller' => ResetPasswordAction::class,
            'validation_groups' => 'put-reset-password'
        ]
    ],
    collectionOperations: ['post' => ['denormalization_context' => ['groups' => 'post'],'normalization_context'=>["groups" => ['get']],'validation_groups'=>'post']]
)]
#[UniqueEntity(fields: "username")]
#[UniqueEntity(fields: "email")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_COMMENTATOR = 'ROLE_COMMENTATOR';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    const DEFAULT_ROLES = [self::ROLE_COMMENTATOR];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     */
    #[Groups(['get'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['get', 'get-comment-with-author', 'get-blogpost-with-author', 'post'])]
    #[Assert\NotBlank(groups: ['post'])]
    #[Assert\Length(min: 6, max: 255, groups: ['post'])]
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Groups('post')]
    #[Assert\Regex(pattern: "/(?=.*[A-Z])(?=.*[a-z]).{7,}/", message: "Password must be seven characters long and contain at least one digit, one uppercase letter")]
    private $password;

    #[Assert\NotBlank(groups: ['post'])]
    #[Groups('post')]
    #[Assert\Expression(expression: "this.getPassword()===this.getRetypedPassword()", message: "Passwords do not match", groups: ['post'])]
    private $retypedPassword;

    #[Assert\NotBlank(groups: ['put-reset-password'])]
    #[Groups('put-reset-password')]
    #[Assert\Regex(pattern: "/(?=.*[A-Z])(?=.*[a-z]).{7,}/", message: "Password must be seven characters long and contain at least one digit, one uppercase letter")]
    private $newPassword;

    #[Assert\NotBlank(groups: ['put-reset-password'])]
    #[Groups('put-reset-password')]
    #[Assert\Expression(expression: "this.getNewPassword()===this.getNewRetypedPassword()", message: "Passwords do not match")]
    private $newRetypedPassword;

    #[Assert\NotBlank(groups: ['put-reset-password'])]
    #[Groups('put-reset-password')]
    #[UserPassword(groups:['put-reset-password'])]
    private $oldPassword;
    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['get', 'post', 'put', 'get-comment-with-author', 'get-blogpost-with-author'])]
    #[Assert\NotBlank(groups: ['post', 'put'])]
    #[Assert\Length(min: 6, max: 255, groups: ['post', 'put'])]
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['post', 'put', 'get-admin', 'get-owner'])]
    #[Assert\NotBlank(groups: ['post'])]
    #[Assert\Length(min: 6, max: 255, groups: ['post', 'put'])]
    #[Assert\Email(groups: ['post', 'put'])]
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author", orphanRemoval=true)
     */
    #[Groups(['get'])]
    private $comments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;
    // #[ORM\OneToMany(targetEntity: BlogPost::class,mappedBy:"author", orphanRemoval:true)]
    // private $posts;


    #[ORM\Column(type: "simple_array")]
    #[Groups(['get-admin', 'get-owner'])]
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=BlogPost::class, mappedBy="author", orphanRemoval=true)
     */
    private $blogPosts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     *@ORM\Column(type="string",length=40, nullable=true)
     */
    private $confirmationToken;

    // ...
    public function getRoles(): array
    {
        $roles = $this->roles;
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->blogPosts = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
        $this->enabled = false;
        $this->confirmationToken = null;
    }
    public function __toString():string
    {
        return $this->name;
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
        $this->retypedPassword = $password;
        return $this;
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
            $blogPost->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->removeElement($blogPost)) {
            // set the owning side to null (unless already changed)
            if ($blogPost->getAuthor() === $this) {
                $blogPost->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of newPassword
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * Set the value of newPassword
     *
     * @return  self
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * Get the value of newRetypedPassword
     */
    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    /**
     * Set the value of newRetypedPassword
     *
     * @return  self
     */
    public function setNewRetypedPassword($newRetypedPassword)
    {
        $this->newRetypedPassword = $newRetypedPassword;

        return $this;
    }

    /**
     * Get the value of oldPassword
     */
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * Set the value of oldPassword
     *
     * @return  self
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /**
     * Get the value of passwordChangeDate
     */
    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }

    /**
     * Set the value of passwordChangeDate
     *
     * @return  self
     */
    public function setPasswordChangeDate($passwordChangeDate)
    {
        $this->passwordChangeDate = $passwordChangeDate;

        return $this;
    }

    /**
     * Get the value of enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the value of enabled
     *
     * @return  self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get 
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }


    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }
}
