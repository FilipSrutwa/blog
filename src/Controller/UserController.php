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
use PHPUnit\Util\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


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
        if ($this->checkUser($userID) == false)
            return $this->redirect('/login');

        $data = [
            'title' => 'Wypok - strona użytkownika',
            'user' => $this->getUser(),
            'posts' => $this->postRepository->findBy(['user' => $userID]),
        ];

        return $this->render('user/index.html.twig', $data);
    }

    #[Route('/editUser/{userID}', name: 'app_user_edit')]
    public function edit(Request $request, $userID): Response
    {
        if ($this->checkUser($userID) == false)
            return $this->redirect('/login');

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
            $imagePath = $userForm->get('ImagePath')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '_photo.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/userImage',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $userToUpdate->setImagePath('/uploads/userImage/' . $newFileName);
                $userToUpdate->setUsername($userForm->get('username')->getData());
                $userToUpdate->setDescription($userForm->get('description')->getData());
                $this->em->persist($userToUpdate);
                $this->em->flush();

                return $this->redirectToRoute('app_user', ['userID' => $userID]);
            } else {
                $userToUpdate->setUsername($userForm->get('username')->getData());
                $userToUpdate->setDescription($userForm->get('description')->getData());

                $this->em->persist($userToUpdate);
                $this->em->flush();

                return $this->redirectToRoute('app_user', ['userID' => $userID]);
            }
        }

        return $this->render('user/editUser.html.twig', $data);
    }

    private function checkUser($userID)
    {
        if ($this->userRepository->find($userID) != $this->getUser())
            return false;
        else
            return true;
    }
}
