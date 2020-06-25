<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Company;
use App\Exception\NotOwnerException;
use Hateoas\HateoasBuilder;
use JMS\Serializer\SerializationContext;
use App\Exception\WrongParameterException;
use App\Exception\RequestStructureException;
use App\Security\UserVoter;
use App\Service\HateoasItemLister;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Hateoas\Representation\PaginatedRepresentation;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="api_users_list", methods={"GET"})
     * @param  mixed $request
     * @return void
     *
     * @SWG\Response(
     *      response=200,
     *      description="Returns the list of users owned by the current company",
     *      @SWG\Schema(
     *          @Model(type=PaginatedRepresentation::class, groups={"Default","users-list"})
     *      )
     * )
     * @SWG\Parameter(
     *      name="page",
     *      in="query",
     *      description="Page number to return.",
     *      type="integer",
     *      default=1
     * )
     * @SWG\Parameter(
     *      name="limit",
     *      in="query",
     *      description="Maximum number of items to return per page.",
     *      type="integer",
     *      default=5
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function usersList(Request $request, UrlGeneratorInterface $urlGeneratorInterface, HateoasItemLister $lister)
    {
        //Sets the repository
        $repo = $this->getDoctrine()->getManager()->getRepository(User::class);

        $json = $lister->getHalJsonResponse($request, $repo, 'api_phones_list', ['Default', 'users-list']);

        return new Response($json, 200, ['Content-Type' => 'application/hal+json']);
    }
   
    /**
     * @Route("/api/users/{id}", name="api_user_details", methods={"GET"})
     * @param  mixed $user
     * @return void
     *
     * @SWG\Response(
     *      response=200,
     *      description="Returns a specific user detail.",
     *      @SWG\Schema(
     *          @Model(type=User::class, groups={"user-details"})
     *      )
     * )
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Id of the user",
     *      required=true,
     *      type="integer",
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function userDetails(User $user, UrlGeneratorInterface $urlGeneratorInterface)
    {
        if (!$this->isGranted(UserVoter::EDIT, $user)) {
            throw new NotOwnerException("You are not allowed to view this user's details");
        }

        //Use Hateoas builder to serialize
        $hateoas = HateoasBuilder::create()
                ->setUrlGenerator(null, new SymfonyUrlGenerator($urlGeneratorInterface))
                ->build();

        $json = $hateoas->serialize($user, 'json', SerializationContext::create()->setGroups(['groups' => 'user-details']));

        return new Response($json, 200, ['Content-Type' => 'application/hal+json']);
    }
 
    /**
     * @Route("/api/users", name="api_add_user", methods={"POST"})
     * @param  mixed $request
     * @return void
     *
     * @SWG\Response(
     *      response=201,
     *      description="Adds a new user.",
     *      @SWG\Schema(
     *          @Model(type=User::class, groups={"user-details"})
     *      )
     * )
     * @SWG\Parameter(
     *      name="userForm",
     *      in="body",
     *      description="Details of the user",
     *      required=true,
     *      type="object",
     *      @Model(type=UserType::class)
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function addUser(Request $request, UrlGeneratorInterface $urlGeneratorInterface)
    {
        $manager = $this->getDoctrine()->getManager();

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
        $user->setCompany($this->getUser());

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
     * @Route("/api/users/{id}", name="api_remove_user", methods={"DELETE"})
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     *
     * @SWG\Response(
     *      response=200,
     *      description="Removes the specified user.",
     *      @SWG\Schema(
     *          @Model(type=User::class, groups={"user-details"})
     *      )
     * )
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Id of the user",
     *      required=true,
     *      type="integer",
     * )
     * @SWG\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function removeUser(Request $request, User $user, UrlGeneratorInterface $urlGeneratorInterface)
    {
        //Checks if company is allowed to remove the user.
        if (!$this->isGranted(UserVoter::EDIT, $user)) {
            throw new NotOwnerException('You are not allowed to delete this user');
        }

        $manager = $this->getDoctrine()->getManager();

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

        return new Response($json, 200, ['Content-Type' => 'application/hal+json']);
    }
}
