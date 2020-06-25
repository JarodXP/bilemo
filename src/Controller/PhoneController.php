<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Phone;
use Hateoas\HateoasBuilder;
use App\Service\HateoasItemLister;
use JMS\Serializer\SerializationContext;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Hateoas\Representation\PaginatedRepresentation;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phones", name="api_phones_list", methods={"GET"})
     * @SWG\Response(
     *      response=200,
     *      description="Returns the list of phones",
     *      @SWG\Schema(
     *          @Model(type=PaginatedRepresentation::class, groups={"Default","phone-list"})
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
     * @SWG\Tag(name="Phones")
     * @Security(name="Bearer")
     */
    public function phonesList(Request $request, UrlGeneratorInterface $urlGeneratorInterface, HateoasItemLister $lister)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Phone::class);

        $json = $lister->getHalJsonResponse($request, $repo, 'api_phones_list', ['Default', 'phone-list']);

        return new Response($json, 200, ['Content-Type' => 'application/hal+json']);
    }

    /**
     * @Route("/api/phones/{id}", name="api_phone_details", methods={"GET"})
     * @SWG\Response(
     *      response=200,
     *      description="Returns a specific phone detail.",
     *      @SWG\Schema(
     *          @Model(type=Phone::class, groups={"phone-details"})
     *      )
     * )
     * @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="Id of the phone",
     *      required=true,
     *      type="integer",
     * )
     * @SWG\Tag(name="Phones")
     * @Security(name="Bearer")
     */
    public function phoneDetails(Phone $phone, UrlGeneratorInterface $urlGeneratorInterface)
    {
        //Use Hateoas builder to serialize
        $hateoas = HateoasBuilder::create()
                ->setUrlGenerator(null, new SymfonyUrlGenerator($urlGeneratorInterface))
                ->build();

        $json = $hateoas->serialize($phone, 'json', SerializationContext::create()->setGroups(['groups' => 'phone-details']));

        return new Response($json, 200, ['Content-Type' => 'application/hal+json']);
    }
}
