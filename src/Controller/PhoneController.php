<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Phone;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PhoneController extends AbstractController
{
    /**
     *@Route("/api/phones", name="api_phones_list", methods={"GET"})
     */
    public function phonesList()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Phone::class);

        return $this->json($repo->findAll(), 200, [], []);
    }

    /**
     *@Route("/api/phones/{id}", name="api_phone_details", methods={"GET"})
     */
    public function phoneDetails(Phone $phone)
    {
        return $this->json($phone, 200, [], []);
    }
}
