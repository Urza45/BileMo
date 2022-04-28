<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * MainController
 */
class MainController extends AbstractController
{
    /**
     * index
     * 
     * @Route("/main", name="app_main")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * customError
     * 
     * @Route("/error404", name="app_error_404")
     *
     * @param  Request $request
     * @return void
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
