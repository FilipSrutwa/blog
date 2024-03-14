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
use Doctrine\ORM\EntityManager;
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
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $data = [
            'title' => 'Wypok - main',
            'posts' => $this->postRepository->findAll(),
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

        $data = [
            'title' => 'Wypok - main',
            'post' => $this->postRepository->find($postNumber),
            'commentForm' => $commentForm->createView(),
            'comments' => $this->commentRepository->findBy(['post' => $this->postRepository->find($postNumber)]),
        ];

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $newComment = $commentForm->getData();
            $newComment->setCreatedAt(new DateTimeImmutable());
            $newComment->setUser($this->getUser());
            $newComment->setPost($this->postRepository->find($postNumber));
            $newComment->setScore(0);

            $this->em->persist($newComment);
            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('main/singlePost.html.twig', $data);
    }
}
