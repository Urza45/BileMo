<?php

namespace App\Controller;

use App\Services\HttpCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * MainController
 */
class MainController extends AbstractController
{
    /**
     * index
     * 
     * @Route("/", name="app_main")
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
     * @Route("/error", name="app_error")
     *
     * @param  Request $request
     * @return void
     */
    public function customError(FlattenException $exception)
    {
        return $this->json(
            [
                $exception->getStatusCode(),
                HttpCode::getHttpMessage($exception->getStatusCode()),
                $exception->getMessage()
            ],
            $exception->getStatusCode()
        );
    }
}
