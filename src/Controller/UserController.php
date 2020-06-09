<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UserController extends AbstractController
{
    /**
     *@Route("/api/users", name="api_users_list", methods={"GET"})
     */
    public function usersList()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(User::class);

        return $this->json($repo->findAll(), 200, [], ['groups' => 'users-list']);
    }

    /**
     *@Route("/api/users/{id}", name="api_user_details", methods={"GET"})
     */
    public function userDetails(User $user)
    {
        return $this->json($user, 200, [], ['groups' => 'users-list']);
    }

    /**
     *@Route("/api/users", name="api_add_user", methods={"POST"})
     */
    public function addUser()
    {
        $message = 'New user added';
        
        return $this->json($message, 201, [], ['groups' => 'users-list']);
    }

    /**
     *@Route("/api/users/{id}", name="api_remove_user", methods={"DELETE"})
     */
    public function removeUser(User $user)
    {
        $body = [
            'message' => 'User removed',
            'user' => $user
        ];
        
        return $this->json($body, 200, [], ['groups' => 'users-list']);
    }
}
