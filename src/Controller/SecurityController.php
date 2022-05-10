<?php

namespace App\Controller;

use App\Entity\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route(name="api_login", path="/api/login_check")
     * @return JsonResponse
     */
    public function api_login(?Client $client): JsonResponse
    {
        //$client = $this->getUser();
        $client = $this->getUser();

        return new JsonResponse([
            'email' => $client->getUserIdentifier(),
            'roles' => $client->getRoles(),
        ]);



        // if (null === $client) {
        //     return $this->json(
        //         [
        //             'client' => $client,
        //             'message' => 'missing credentials',
        //         ],
        //         Response::HTTP_UNAUTHORIZED
        //     );
        // }

        // $token = 'fbfdbfdbdfbfddfbfdbdfb';

        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/ApiLoginController.php',
        //     'user'  => $client->getUserIdentifier(),
        //     'token' => $token,
        // ]);
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
