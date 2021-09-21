<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
   
    private $faker;
    #TODO assign right roles to users
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
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
        for ($i=0; $i < 20; $i++) { 
            $num = rand(0,9);
            $user =$this->getReference('user'.$num);
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->sentence());
            $blogPost->setContent($this->faker->paragraph());
            $blogPost->setSlug(str_replace(' ','-',strtolower($blogPost->getTitle())));
            $blogPost->setPublished($this->faker->dateTime());
            $blogPost->setAuthor($user);
            $this->addReference('blog_post'.$i,$blogPost);
            $manager->persist($blogPost);
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        for ($i=0; $i <10 ; $i++) { 
            $user = new User();
            $user->setUsername($this->faker->userName());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'secret123'));
            $user->setName($this->faker->name());
            $user->setEmail($this->faker->email());
            $manager->persist($user);
            $this->addReference('user'.$i,$user);
        }
      
        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i=0; $i <20 ; $i++) { 
           for ($j=0; $j <rand(1,5) ; $j++) { 
                $num = rand(0,9);
                $user =$this->getReference('user'.$num);
                $comment = new Comment();
                $comment->setContent($this->faker->paragraph());
                $comment->setAuthor($user);
                $comment->setPublished($this->faker->dateTimeBetween('-2 years'));
                $post = $this->getReference('blog_post'.$i);
                $comment->setPost($post);
                $manager->persist($comment);
           }
        }
        $manager->flush();
    }


}
