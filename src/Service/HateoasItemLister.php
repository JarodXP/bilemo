<?php

declare(strict_types=1);

namespace App\Service;

use Hateoas\HateoasBuilder;
use JMS\Serializer\SerializationContext;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class HateoasItemLister
{
    private array $_listParams = [];
    private array $_embeddedItems = [];
    private UrlGeneratorInterface $_urlGeneratorInterface;

    public function __construct(UrlGeneratorInterface $urlGeneratorInterface)
    {
        $this->_urlGeneratorInterface = $urlGeneratorInterface;
    }
    
    /**
     * getHalJsonResponse
     *
     * @param  mixed $request
     * @param  mixed $repo
     * @param  mixed $route
     * @param  mixed $groups
     * @return string :json
     */
    public function getHalJsonResponse(Request $request, ServiceEntityRepository $repo, string $route, array $groups):string
    {
        //Gets a Hateoas PaginatedRepresentation with the embedded items and params
        $paginatedCollection = $this->getPaginatedCollection($request, $repo, $route);

        //Use Hateoas builder to serialize
        $hateoas = HateoasBuilder::create()
                ->setUrlGenerator(null, new SymfonyUrlGenerator($this->_urlGeneratorInterface))
                ->build();

        return $hateoas->serialize($paginatedCollection, 'json', SerializationContext::create()->setGroups($groups));
    }
    
    /**
     * getPaginatedCollection
     * Gets the PaginatedRepresentation object containing both the array of embedded items and the list parameters
     *
     * @param  mixed $request
     * @param  mixed $repo
     * @param  mixed $route
     * @return PaginatedRepresentation
     */
    private function getPaginatedCollection(Request $request, ServiceEntityRepository $repo, string $route):PaginatedRepresentation
    {
        //Gets the list of items
        $this->queryItems($request, $repo);

        $items = $this->_embeddedItems;

        //Use the PaginatedRepresentation to build the collection and the hypertext params
        return new PaginatedRepresentation(
            new CollectionRepresentation(
                $items
            ),
            $route,
            [],
            $this->_listParams['currentPage'],
            $this->_listParams['limit'],
            $this->_listParams['totalPages'],
            null,
            null,
            false,
            $this->_listParams['totalItems']
        );
    }

    /**
     * queryItems
     * Gets the list of items and transforms it into an array
     * @param  mixed $request
     * @param  mixed $repo
     * @return void
     */
    private function queryItems(Request $request, ServiceEntityRepository $repo): void
    {
        //Sets the query parameters
        $page = (int) $request->query->get('page');
        $limit = (int) $request->query->get('limit');

        //Gets a Paginator object with the list of items
        $paginatorList = $repo->findList($page, $limit);

        //Converts the Paginator object into an array of users to be transmitted to the PaginatedRepresentation
        foreach ($paginatorList as $item) {
            $this->_embeddedItems[] = $item;
        }

        //Sets the list parameters
        $this->_listParams['currentPage'] = ($paginatorList->getQuery()->getFirstResult()/$paginatorList->getQuery()->getMaxResults())+1;
        $this->_listParams['limit'] = $paginatorList->getQuery()->getMaxResults();
        $this->_listParams['totalPages'] = (int) ceil(count($paginatorList)/$paginatorList->getQuery()->getMaxResults());
        $this->_listParams['totalItems'] = count($paginatorList);
    }
}
