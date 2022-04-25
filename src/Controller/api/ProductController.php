<?php

namespace App\Controller\api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class ProductController extends AbstractController
{
    /**
     * Return list of product in a json response
     * 
     * @Route("/api/products", name="api_product_list", methods={"GET"})
     */
    public function showProductsList(ProductRepository $repoProduct, SerializerInterface $serializer): Response
    {
        // $products = $repoProduct->findAll();

        // $data = $serializer->serialize(
        //     $products,
        //     'json',
        //     ['groups' => 'list_product']
        // );
        // Méthode 1
        // $response = new Response($data);
        // $response->headers->set('Content-Type', 'application/json');

        // Méthode 2
        // $response = new Response($data, 200, [
        //     'content-type' => 'application/json'
        // ]);

        // Méthode 3
        // $response = new JsonResponse($data, 200, [], true);

        // Méthode 4 : une seule ligne pour tout faire
        //$response = $this->json($products, 200, [], ['groups' => 'list_product']);

        // Méthode 5 on refactorise encore
        return $this->json($repoProduct->findAll(), 200, [], ['groups' => 'list_product']);

        //return $response;
    }

    /**
     * Return a product in a json response
     * 
     * @Route("/api/products/{index}", name="api_product_show", methods={"GET"}, requirements={"index"="\d+"})
     */
    public function showProduct(ProductRepository $repoProduct, SerializerInterface $serializer, Request $request): Response
    {
        $product = $repoProduct->findOneBy(['id' => $request->get('index')]);

        $data = $serializer->serialize(
            $product,
            'json',
            ['groups' => 'show_product']
        );

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Add a product from a json request
     * 
     * @Route("/api/products", name="api_product_add", methods={"POST"})
     */
    public function addProduct(ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();

        try {
            $product = $serializer->deserialize($data, Product::class, 'json');

            $errors = $validator->validate($product);

            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $manager = $doctrine->getManager();
            $manager->persist($product);
            $manager->flush();

            // return new Response('', Response::HTTP_CREATED);
            return $this->json($product, Response::HTTP_CREATED, [], ['groups' => 'show_product']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (NotNormalizableValueException $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
