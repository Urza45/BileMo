<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/api", name="app_api_api")
     * 
     * 
     * 
     */
    public function index(): Response
    {
        return $this->render('api/api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}
