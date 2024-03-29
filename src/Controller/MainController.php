<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Form\PostFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    private $postRepository;
    private $commentRepository;
    private $em;
    public function __construct(PostRepository $postRepository, CommentRepository $commentRepository, EntityManagerInterface $em)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->em = $em;
    }

    #[Route('/', name: 'app_main')]
    public function index(Request $request): Response
    {
        $postForm = new Post();
        $form = $this->createForm(PostFormType::class, $postForm);

        $posts = $this->postRepository->findBy(array(), ['createdAt' => 'DESC']);
        $postsWithTwoComments = array();
        $i = 0;

        foreach ($posts as $post) {
            $postsWithTwoComments[$i][0] = $post;
            $postsWithTwoComments[$i][1] = $post->getTwoComments();
            $i++;
        }

        $data = [
            'title' => 'Wypok - strona główna',
            'posts' => $postsWithTwoComments,
            'form' => $form->createView(),
        ];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();
            $newPost->setUser($this->getUser());
            $newPost->setCreatedAt(new DateTimeImmutable());
            $newPost->setScore(0);

            $this->em->persist($newPost);
            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('main/index.html.twig', $data);
    }

    #[Route('/post/{postNumber}', name: 'app_main_post')]
    public function singlePost(Request $request, $postNumber): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $post = $this->postRepository->find(($postNumber));
        $data = [
            'title' => 'Wypok',
            'post' => $post,
            'commentForm' => $commentForm->createView(),
            'comments' => $this->commentRepository->findBy(['post' => $post]),
        ];

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $newComment = $commentForm->getData();
            $newComment->setCreatedAt(new DateTimeImmutable());
            $newComment->setUser($this->getUser());
            $newComment->setPost($post);
            $newComment->setScore(0);

            $this->em->persist($newComment);
            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('main/singlePost.html.twig', $data);
    }
}
