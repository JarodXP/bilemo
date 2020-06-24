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

class PhoneController extends AbstractController
{
    /**
     *@Route("/api/phones", name="api_phones_list", methods={"GET"})
     */
    public function phonesList(Request $request, UrlGeneratorInterface $urlGeneratorInterface, HateoasItemLister $lister)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Phone::class);

        $json = $lister->getHalJsonResponse($request, $repo, 'api_phones_list', ['Default', 'phone-list']);

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
