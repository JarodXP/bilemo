<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Exception\RequestStructureException;
use App\Exception\WrongParameterException;
use App\Form\UserType;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * usersList
     * @Route("/api/users", name="api_users_list", methods={"GET"})
     * @param  mixed $request
     * @return void
     */
    public function usersList(Request $request)
    {
        $page = (int) $request->query->get('page');
        $limit = (int) $request->query->get('limit');

        $repo = $this->getDoctrine()->getManager()->getRepository(User::class);

        $response = [
            'hypermedia' => 'There will be some links',
            'page' => $page,
            'limit' => $limit,
            'phones' => $repo->findUserList($page, $limit)
        ];

        return $this->json($response, 200, [], ['groups' => 'users-list']);
    }
   
    /**
     * userDetails
     * @Route("/api/users/{id}", name="api_user_details", methods={"GET"})
     * @param  mixed $user
     * @return void
     */
    public function userDetails(User $user)
    {
        $response = [
            'hypermedia' => 'There will be some links',
            'user details' => $user
        ];

        return $this->json($response, 200, [], ['groups' => 'user-details']);
    }
 
    /**
     * addUser
     * @Route("/api/users", name="api_add_user", methods={"POST"})
     * @param  mixed $request
     * @param  mixed $serializer
     * @param  mixed $validator
     * @return void
     */
    public function addUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $manager = $this->getDoctrine()->getManager();

        if ($request->query->get('companyId') !== null) {
            $company = $manager->getRepository(Company::class)->findOneBy(['id' => $request->query->get('companyId')]);
        } else {
            throw new WrongParameterException('Missing company Id');
        }

        //Gets the user data from the request body
        $userData = json_decode($request->getContent(), true);

        //Uses the form component to hydrate the user
        $user = new User;
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->submit($userData);

        //Gets the validation errors and calls the error handler
        if (!$userForm->isValid()) {
            $errorMessages = [];

            $errors = $userForm->getErrors(true);

            for ($i = 0; $i < count($errors); $i++) {
                $errorMessages[$i]['fieldName'] = $errors[$i]->getOrigin()->getName();
                $errorMessages[$i]['message'] = $errors[$i]->getMessage();
            }

            throw new RequestStructureException(json_encode($errorMessages));
        }

        //Sets the company
        $user->setCompany($company);

        $manager->persist($user);

        $manager->flush();
        
        $response = [
            'hypermedia' => 'There will be some links',
            'message' => 'New user added',
            'user' => $user
        ];
        
        return $this->json($response, 201, ['charset' => 'UTF-8'], ['groups' => 'user-details']);
    }

    /**
     * removeUser
     * @Route("/api/users/{id}", name="api_remove_user", methods={"DELETE"})
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     */
    public function removeUser(Request $request, User $user)
    {
        $manager = $this->getDoctrine()->getManager();

        //Compares the company of the requester with the company the user belongs to.
        $companyId = $request->query->get('companyId');

        if ($companyId != $user->getCompany()->getId()) {

            //If the user doesn't belong to the company, throws exception
            throw new Exception('You are not allowed to remove a user that doesn\'t belog to your company');
        }

        //Stores the user id to be implemented in the message
        $userId = $user->getId();

        $manager->remove($user);

        $manager->flush();

        $response = [
            'hypermedia' => 'There will be some links',
            'message' => 'User '.$userId.' removed',
        ];
        
        return $this->json($response, 200, [], ['groups' => 'users-list']);
    }
}
