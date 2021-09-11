<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogPostController extends AbstractController
{
    private const POSTS= [
        [
            'id'=>1,
            'slug'=> 'hello-world',
            'title'=> 'Hello World!'
        ],
        [
            'id'=>2,
            'slug'=> 'another-post',
            'title'=> 'Another Post!'
        ],
        [
            'id'=>3,
            'slug'=> 'last-example',
            'title'=> 'Last Example'
        ],
    ];

    #[Route('/posts', name: 'blog_list',defaults:['page'=>5],requirements:['page'=>'\d+'])]
    public function index(Request $request,$page=1 ): Response
    {
        $limit= $request->get('limit',10);
        $items= $this->getDoctrine()->getRepository(BlogPost::class)->findAll(); 
        return $this->json([
            'page' => $page,
            'limit'=> $limit,
            'data' => array_map(function($item){
                return $this->generateUrl('blog_by_slug',['slug'=>$item->getSlug()]);
            },$items)
        ]);
    }

    #[Route('/post/{id}',name:'blog_by_id',requirements:['id'=>'\d+'])]
    public function postById(BlogPost $post)
    {
        // works thanks to params converter
        return $this->json($post); 
    }

    #[Route('/post/{slug}', name: "blog_by_slug")]
    public function postBySlug($slug)
    {
        //we could also use params converter
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug'=>$slug])
            //self::POSTS[array_search($slug,array_column(self::POSTS,'slug'))]
        );
    }

    #[Route("/posts/add",name:'blog_add',methods:['POST'])]
    public function add(Request $request)
    {
        /**@var Serializer $serializer */
        $serializer= $this->get('serializer');
        
        $blogPost= $serializer->deserialize($request->getContent(),BlogPost::class,'json');
        $blogPost->setSlug(str_replace(' ','-',strtolower($blogPost->getTitle()) ) );
        $em= $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    #[Route("/posts/delete/{id}",methods:['DELETE'],requirements:['id'=>'\d+'])]
    public function delete($id)
    {
        
        $em= $this->getDoctrine()->getManager();
        $post = $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['id'=>$id]);
        $em->remove($post);
        $em->flush();
        return $this->json(null,Response::HTTP_NO_CONTENT);
    }
}
