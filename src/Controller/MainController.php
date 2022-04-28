<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/main", name="app_main")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/error404", name="app_error_404")
     */
    public function customError(Request $request)
    {
        return $this->json(
            [
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $request->attributes->get('exception')->getMessage()
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
