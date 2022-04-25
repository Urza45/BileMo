<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * Return list of product in a json response
     * 
     * @Route("/api/products", name="api_product_list", methods="GET")
     */
    public function index(ProductRepository $repoProduct, SerializerInterface $serializer): Response
    {
        $products = $repoProduct->findAll();

        $data = $serializer->serialize($products, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
