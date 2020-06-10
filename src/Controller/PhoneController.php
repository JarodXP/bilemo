<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Phone;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PhoneController extends AbstractController
{
    /**
     *@Route("/api/phones", name="api_phones_list", methods={"GET"})
     */
    public function phonesList(Request $request)
    {
        $page = (int) $request->query->get('page');
        $limit = (int) $request->query->get('limit');

        $repo = $this->getDoctrine()->getManager()->getRepository(Phone::class);

        $response = [
            'hypermedia' => 'There will be some links',
            'page' => $page,
            'limit' => $limit,
            'phones' => $repo->findPaginatedList($page, $limit)
        ];

        return $this->json($response, 200, [], ['groups' => 'phone-list']);
    }

    /**
     *@Route("/api/phones/{id}", name="api_phone_details", methods={"GET"})
     */
    public function phoneDetails(Phone $phone)
    {
        $response = [
            'hypermedia' => 'There will be some links',
            'phone details' => $phone
        ];

        return $this->json($response, 200, [], ['phone-details']);
    }
}
