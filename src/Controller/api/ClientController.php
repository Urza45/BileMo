<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/api/client", name="app_api_client")
     */
    public function index(): Response
    {
        return $this->render('api/client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }
}
