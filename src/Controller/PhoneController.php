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
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phones", name="api_phones_list", methods={"GET"})
     * @SWG\Get(
     *      description="Endpoint for the list of available phones",
     *      produces={"application/hal+json"},
     *      @SWG\Response(
     *          response=200,
     *          description="Returns the list of phones",
     *          @SWG\Schema(
     *              @Model(type=PaginatedRepresentation::class, groups={"Default","phone-list"})
     *          )
     *      ),
     *      @SWG\Parameter(
     *          name="page",
     *          in="query",
     *          description="Page number to return.",
     *          type="integer",
     *          default=1
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Maximum number of items to return per page.",
     *          type="integer",
     *          default=5
     *      )
     * )
     * @SWG\Tag(name="Phones")
     * @Security(name="Bearer")
     */
    public function phonesList(Request $request, UrlGeneratorInterface $urlGeneratorInterface, HateoasItemLister $lister, CacheInterface $cache)
    {
        //Sets item name corresponding to the request parameters
        $itemName = 'phone-list';
        
        if ($request->query->get('page') != null) {
            $itemName .= '-'.$request->query->get('page');
        }

        if ($request->query->get('limit') != null) {
            $itemName .= '-'.$request->query->get('limit');
        }

        //Gets phone list from cache in priority
        $phoneList = $cache->get($itemName, function (ItemInterface $item) use ($request, $lister) {
            $item->expiresAfter(120);

            $repo = $this->getDoctrine()->getManager()->getRepository(Phone::class);

            return $lister->getHalJsonResponse($request, $repo, 'api_phones_list', ['Default', 'phone-list']);
        });

        return new Response($phoneList, 200, ['Content-Type' => 'application/hal+json']);
    }

    /**
     * @Route("/api/phones/{id}", name="api_phone_details", methods={"GET"})
     * @SWG\Get(
     *      description="Endpoint for a specific phone's details",
     *      produces={"application/hal+json"},
     *      @SWG\Response(
     *          response=200,
     *          description="Returns a specific phone detail.",
     *          @SWG\Schema(
     *              @Model(type=Phone::class, groups={"phone-details"})
     *          )
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id of the phone",
     *          required=true,
     *          type="integer",
     *      )
     * )
     * @SWG\Tag(name="Phones")
     * @Security(name="Bearer")
     */
    public function phoneDetails(Phone $phone, UrlGeneratorInterface $urlGeneratorInterface, CacheInterface $cache)
    {
        //Gets phone details from cache in priority
        $serializedPhoneDetails = $cache->get('phone'.'-'.$phone->getId(), function (ItemInterface $item) use ($urlGeneratorInterface, $phone) {
            $item->expiresAfter(3600);
            
            //Use Hateoas builder to serialize
            $hateoas = HateoasBuilder::create()
                    ->setUrlGenerator(null, new SymfonyUrlGenerator($urlGeneratorInterface))
                    ->build();

            return $hateoas->serialize($phone, 'json', SerializationContext::create()->setGroups(['groups' => 'phone-details']));
        });
        
        return new Response($serializedPhoneDetails, 200, ['Content-Type' => 'application/hal+json']);
    }
}
