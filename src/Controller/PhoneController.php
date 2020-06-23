<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Phone;
use Hateoas\HateoasBuilder;
use JMS\Serializer\SerializationContext;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhoneController extends AbstractController
{
    /**
     *@Route("/api/phones", name="api_phones_list", methods={"GET"})
     */
    public function phonesList(Request $request, UrlGeneratorInterface $urlGeneratorInterface)
    {
        //Sets the query parameters
        $page = (int) $request->query->get('page');
        $limit = (int) $request->query->get('limit');

        $repo = $this->getDoctrine()->getManager()->getRepository(Phone::class);

        //Gets the list of phones
        $paginatorList = $repo->findPhoneList($page, $limit);

        //Converts the Paginator object into an array of phones to be transmitted to the PaginatedRepresentation
        foreach ($paginatorList as $phone) {
            $phones[] = $phone;
        };

        //Use the PaginatedRepresentation to build the collection and the hypertext params
        $paginatedCollection = new PaginatedRepresentation(
            new CollectionRepresentation(
                $phones
            ),
            'api_phones_list',
            [],
            ($paginatorList->getQuery()->getFirstResult()/$paginatorList->getQuery()->getMaxResults())+1,
            $paginatorList->getQuery()->getMaxResults(),
            (int) ceil(count($paginatorList)/$paginatorList->getQuery()->getMaxResults()),
            null,
            null,
            false,
            count($paginatorList)
        );

        //Use Hateoas builder to serialize
        $hateoas = HateoasBuilder::create()
                ->setUrlGenerator(null, new SymfonyUrlGenerator($urlGeneratorInterface))
                ->build();

        $json = $hateoas->serialize($paginatedCollection, 'json', SerializationContext::create()->setGroups(['Default', 'phone-list']));

        return new Response($json, 200, ['Content-Type' => 'application/hal+json']);
    }

    /**
     *@Route("/api/phones/{id}", name="api_phone_details", methods={"GET"})
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
