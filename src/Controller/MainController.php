<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Post;
use App\Form\PostFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class MainController extends AbstractController
{
    private $postRepository;
    private $em;
    public function __construct(PostRepository $postRepository, EntityManagerInterface $em)
    {
        $this->postRepository = $postRepository;
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
        }

        return $this->render('main/index.html.twig', $data);
    }

    #[Route('/post/{postNumber}', name: 'app_main_post', methods: ['GET', 'HEAD'])]
    public function singlePost(): Response
    {
        $data = ['title' => 'Wypok - main'];
        return $this->render('main/singlePost.html.twig', $data);
    }
}
