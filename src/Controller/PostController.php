<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\File as FileValidator;

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
     * App\Services\FileUploader is a custom Service class, modify as needed.
     *
     * @Route("/create", name="create")
     * @param Request $request
     * @param FileUploader $fu
     * @return Response
     */
    public function create(Request $request, FileUploader $fu) {
        // create a new post with title
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted()) {
            // entity manager
            $em = $this->getDoctrine()->getManager();
            /** @var UploadedFile $file */
            $file = $request->files->get('post')['attachment'];
            if($file) {
                if($file->getError() === 1) {
                    // This could be due to the file being larger than php_ini upload_max_filesize
                    throw new RuntimeException("There was an error uploading your file.");
                }

                // Using Symfony Validator with File Constraints
                $validator = Validation::createValidator();
                $violations = $validator->validate($file,
                    new FileValidator([
                        'maxSize'   => '3M',
                        'mimeTypes' => [
                            'image/apng',
                            'image/bmp',
                            'image/gif',
                            'image/x-icon',
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                            'image/tiff',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file.'
                    ])
                );
                if(count($violations) > 0) {
                    throw new RuntimeException($violations[0]->getMessage());
                }

                $filename = $fu->uploadFile($file);
                if(is_null($filename)) {
                    throw new RuntimeException("Could not determine uploaded file extension");
                }
                $post->setImage($filename);
            }
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

        if(empty($post)) {
            throw $this->createNotFoundException('This post does not exist.');
        }

        return $this->render('post/show.html.twig', [
            'post'  => $post
        ]);
    }

    /**
     * This is a view that demos how to get Post/Entity data using a custom method in the Post/Entity Repository class.
     *
     * @Route("/showalt/{id}", name="showalt")
     * @param $id
     * @param PostRepository $pr
     * @return Response
     */
    public function showalt($id, PostRepository $pr) {
        $post = $pr->findPostByIdAndIncludeCategoryData($id);

        if(empty($post)) {
            throw $this->createNotFoundException('This post does not exist.');
        }

        return $this->render('post/showalt.html.twig', [
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
