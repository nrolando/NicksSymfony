<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /* Symfony does its own Dependency Injections on controller actions here.
     * See Symfony\Component\HttpKernel\HttpKernel::handleRaw() @ /vendor/symfony/http-kernel/HttpKernel.php
     */
    /**
     * @Route("/", name="index")
     * @param PostRepository $postRepo
     * @return Response
     */
    public function index(PostRepository $postRepo) {
        $posts = $postRepo->findAll();

        // Get the Symfony dump method from recipe "symfony/var-dumper". May already come bundled with symfony/skeleton as of Symfony 5.
        // composer require dump
        //dump($posts);

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request) {
        // create a new post with title
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            // entity manager
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirect($this->generateUrl('post.index'));
        }

        // return a response
        return $this->render('post/create.html.twig', [
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     * @param $id
     * @param PostRepository $pr
     * @return Response
     */
    public function show($id, PostRepository $pr) {
        $post = $pr->find($id);

        return $this->render('post/show.html.twig', [
            'post'  => $post
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param $id
     * @param PostRepository $pr
     */
    public function remove($id, PostRepository $pr) {
        $em = $this->getDoctrine()->getManager();

        $em->remove($pr->find($id));
        $em->flush();

        return new JsonResponse(array(
            'msg'       => 'success',
            'postId'    => $id
        ));
    }
}
