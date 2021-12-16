<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $faker;
    #TODO assign right roles to users
    const USERS = [
        [
            'username' => 'superadminuser',
            'name' => 'super admin',
            'email' => 'superadmin@api.com',
            'password' => 'admin',
            'roles' => [User::ROLE_SUPERADMIN],
            'enabled' => true
        ],
        [
            'username' => 'adminuser',
            'name' => 'admin user',
            'email' => 'admin@api.com',
            'password' => 'admin',
            'roles' => [User::ROLE_ADMIN],
            'enabled' => true
        ]

    ];
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private TokenGenerator $tokenGenerator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = \Faker\Factory::create();
    }
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $num = rand(0, 9);
            $user = $this->getReference('user' . $num);
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->sentence());
            $blogPost->setContent($this->faker->paragraph());
            $blogPost->setSlug(str_replace(' ', '-', strtolower($blogPost->getTitle())));
            $blogPost->setPublished($this->faker->dateTime());
            $blogPost->setAuthor($user);
            $this->addReference('blog_post' . $i, $blogPost);
            $manager->persist($blogPost);
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $roles = [User::ROLE_WRITER, User::ROLE_EDITOR, User::ROLE_COMMENTATOR];
        for ($i = 0; $i < 10; $i++) {
            $num = rand(0, 2);
            $user = new User();
            $user->setUsername($this->faker->userName());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'secret123'));
            $user->setName($this->faker->name());
            $user->setEmail($this->faker->email());
            $user->setRoles([$roles[$num]]);
            $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
            $this->addReference('user' . $i, $user);
        }
        foreach (self::USERS as $fakeUser) {
            $user = new User();
            $user->setUsername($fakeUser['username']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $fakeUser['password']));
            $user->setName($fakeUser['name']);
            $user->setEmail($fakeUser['email']);
            $user->setRoles($fakeUser['roles']);
            $user->setEnabled(true);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            for ($j = 0; $j < rand(1, 5); $j++) {
                $num = rand(0, 9);
                $user = $this->getReference('user' . $num);
                $comment = new Comment();
                $comment->setContent($this->faker->paragraph());
                $comment->setAuthor($user);
                $comment->setPublished($this->faker->dateTimeBetween('-2 years'));
                $post = $this->getReference('blog_post' . $i);
                $comment->setPost($post);
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
