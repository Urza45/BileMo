<?php

namespace App\Controller;

use App\Entity\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class SecurityController extends AbstractController
{
    /**
     * @Route(name="api_login", path="/api/login_check", methods={"POST"})
     * 
     * @OA\Post(
     *      description="List the characteristics of the specified client",
     *      tags={"Authentication"},
     *      @OA\Response(
     *          response=200,
     *          description="Returns the rewards of an user",
     *      )
     * )
     * 
     * @return JsonResponse
     */
    public function api_login(?Client $client): JsonResponse
    {
        $client = $this->getUser();

        return new JsonResponse([
            'email' => $client->getUserIdentifier(),
            'roles' => $client->getRoles(),
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
