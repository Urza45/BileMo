<?php

namespace App\Controller\Api;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ClientController extends AbstractController
{
    /**
     * @Route("/api/clients", name="app_api_client")
     * @OA\Get(
     *      description="List the characteristics of the specified client (Restricted to admin)",
     *      tags={"Client"},
     *      @OA\Response(
     *          response=200,
     *          description="Returns the rewards of an user",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Client::class, groups={"list_client"}))
     *          )
     *      )
     * )
     */
    public function index(): Response
    {
        return $this->render('api/client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }
}
