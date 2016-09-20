<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\PostType;

/**
 * @Route("/posts")
 */
class PostController extends Controller
{
    /**
     * @Route("/", name="post_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('post/index.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/posts/{slug}", name="post")
     * @Method("GET")
     */
    public function postAction(Post $post)
    {
        return $this->render('post/post.html.twig', ['post' => $post]);
    }


    /**
     * @Route("delete/{id}", requirements={"id": "\d+"}, name="post_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Post $post)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('Success', 'Post Deleted');

        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="post_edit")
     * @Method({"POST", "GET"})
     */
    public function editAction(Post $post, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(PostType::class, $post);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $post->setSlug($this->get('slugify')->slugify($post->getTitle()));
            $entityManager->flush();

            $this->addFlash('Success', 'Post Edited');

            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/edit.html.twig', [
            'post'   => $post,
            'form'   => $editForm->createView(),
        ]);
    }

    /**
     * @Route("/add", name="post_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        $post = new Post();
        $post->setAuthor("thrust");

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $post->setSlug($this->get('slugify')->slugify($post->getTitle()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('Success', 'Post created.');

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/add.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}

