<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main', methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        $data = ['title' => 'Wypok - main'];
        return $this->render('main/index.html.twig', $data);
    }

    #[Route('/post/{postNumber}', name: 'app_main_post', methods: ['GET', 'HEAD'])]
    public function singlePost(): Response
    {
        $data = ['title' => 'Wypok - main'];
        return $this->render('main/singlePost.html.twig', $data);
    }
}
