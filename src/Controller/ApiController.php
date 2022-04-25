<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * Return list of product in a json response
     * 
     * @Route("/api/products", name="api_product_list", methods={"GET"})
     */
    public function showProductsList(ProductRepository $repoProduct, SerializerInterface $serializer): Response
    {
        $products = $repoProduct->findAll();

        $data = $serializer->serialize($products, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Return list of product in a json response
     * 
     * @Route("/api/products/{index}", name="api_product", methods={"GET"}, requirements={"index"="\d+"})
     */
    public function showProduct(ProductRepository $repoProduct, SerializerInterface $serializer, Request $request): Response
    {
        $product = $repoProduct->findOneBy(['id' => $request->get('index')]);

        $data = $serializer->serialize($product, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
