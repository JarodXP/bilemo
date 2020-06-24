<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Company;
use Hateoas\HateoasBuilder;
use JMS\Serializer\SerializationContext;
use App\Exception\WrongParameterException;
use App\Exception\RequestStructureException;
use App\Service\HateoasItemLister;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * usersList
     * @Route("/api/users", name="api_users_list", methods={"GET"})
     * @param  mixed $request
     * @return void
     */
    public function usersList(Request $request, UrlGeneratorInterface $urlGeneratorInterface, HateoasItemLister $lister)
    {
        $company = $this->getUser();
        
        //Sets the repository
        $repo = $this->getDoctrine()->getManager()->getRepository(User::class);

        $json = $lister->getHalJsonResponse($request, $repo, 'api_phones_list', ['Default', 'users-list']);

        return new Response($json, 200, ['Content-Type' => 'application/hal+json']);
    }
   
    /**
     * userDetails
     * @Route("/api/users/{id}", name="api_user_details", methods={"GET"})
     * @param  mixed $user
     * @return void
     */
    public function userDetails(User $user, UrlGeneratorInterface $urlGeneratorInterface)
    {
        //Use Hateoas builder to serialize
        $hateoas = HateoasBuilder::create()
                ->setUrlGenerator(null, new SymfonyUrlGenerator($urlGeneratorInterface))
                ->build();

        $json = $hateoas->serialize($user, 'json', SerializationContext::create()->setGroups(['groups' => 'user-details']));

        return new Response($json, 200, ['Content-Type' => 'application/hal+json']);
    }
 
    /**
     * addUser
     * @Route("/api/users", name="api_add_user", methods={"POST"})
     * @param  mixed $request
     * @return void
     */
    public function addUser(Request $request, UrlGeneratorInterface $urlGeneratorInterface)
    {
        $manager = $this->getDoctrine()->getManager();

        //Checks if company Id is set and sets the company
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
        
        //Builds the response to be serialized
        $response = [
            'message' => 'New user added',
            'user' => $user
        ];

        //Use Hateoas builder to serialize
        $hateoas = HateoasBuilder::create()
                ->setUrlGenerator(null, new SymfonyUrlGenerator($urlGeneratorInterface))
                ->build();

        $json = $hateoas->serialize($response, 'json', SerializationContext::create()->setGroups(['user-details']));

        return new Response($json, 201, ['Content-Type' => 'application/hal+json']);
    }

    /**
     * removeUser
     * @Route("/api/users/{id}", name="api_remove_user", methods={"DELETE"})
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     */
    public function removeUser(Request $request, User $user, UrlGeneratorInterface $urlGeneratorInterface)
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

        //Builds the response to be serialized
        $response = [
            'message' => 'User '.$userId.' removed',
            '_links' => [
                'Add user' => [
                    'href' => $this->generateUrl('api_add_user'),
                    'method' => 'POST'
                ],
                'Get list' => [
                    'href' => $this->generateUrl('api_users_list'),
                    'method' => 'GET'
                ]
            ]
        ];

        //Use Hateoas builder to serialize
        $hateoas = HateoasBuilder::create()
                ->setUrlGenerator(null, new SymfonyUrlGenerator($urlGeneratorInterface))
                ->build();

        $json = $hateoas->serialize($response, 'json');

        return new Response($json, 201, ['Content-Type' => 'application/hal+json']);
    }
}
