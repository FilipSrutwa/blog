<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\UserFormType;

class UserController extends AbstractController
{
    private $em;
    private $postRepository;
    private $userRepository;
    public function __construct(PostRepository $postRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/user/{userID}', name: 'app_user')]
    public function index($userID, Request $request): Response
    {
        $data = [
            'title' => 'Wypok - strona użytkownika',
            'user' => $this->getUser(),
        ];

        return $this->render('user/index.html.twig', $data);
    }

    #[Route('/editUser/{userID}', name: 'app_user_edit')]
    public function edit(Request $request, $userID): Response
    {
        $user = $this->getUser();
        $userForm = $this->createForm(UserFormType::class, $user);

        $data = [
            'title' => 'Wypok - strona użytkownika',
            'user' => $this->getUser(),
            'userForm' => $userForm,
        ];

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $userToUpdate = $this->userRepository->find($userID);

            if ($userForm->get('ImagePath')->getData()) {
            } else {
                $userToUpdate->setUsername($userForm->get('username')->getData());
                $userToUpdate->setDescription($userForm->get('description')->getData());

                $this->em->flush();
                return $this->redirect($request->getUri());
            }
        }

        return $this->render('user/editUser.html.twig', $data);
    }
}
