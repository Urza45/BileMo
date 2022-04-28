<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * ProductController
 */
class ProductController extends AbstractController
{
    /**
     * showProductsList
     * Return list of product in a json response
     * 
     * @Route("/api/products", name="api_product_list", methods={"GET"})
     *
     * @param  ProductRepository $repoProduct
     * @return Response
     */
    public function showProductsList(ProductRepository $repoProduct): Response
    {
        return $this->json(
            $repoProduct->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'list_product']
        );
    }

    /**
     * showProduct
     * Return a product in a json response
     * 
     * @Route("/api/products/{id}", name="api_product_show", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @param  Product $product
     * @return Response
     */
    public function showProduct(Product $product = null): Response
    {
        if ($product) {
            return $this->json($product, Response::HTTP_OK, [], ['groups' => 'show_product']);
        }

        return $this->json(
            [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Le produit recherché n\'a pas été trouvé'
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * addProduct
     * Add a product from a json request
     * 
     * @Route("/api/products", name="api_product_add", methods={"POST"})
     *
     * @param  ManagerRegistry $doctrine
     * @param  SerializerInterface $serializer
     * @param  Request $request
     * @param  ValidatorInterface $validator
     * @return Response
     */
    public function addProduct(ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();

        try {
            $product = $serializer->deserialize($data, Product::class, 'json');

            $product->setCreatedAt(new \Datetime);
            $errors = $validator->validate($product);

            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $manager = $doctrine->getManager();
            $manager->persist($product);
            $manager->flush();

            return $this->json(
                $product,
                Response::HTTP_CREATED,
                [],
                ['groups' => 'show_product']
            );
        } catch (NotEncodableValueException $e) {
            return $this->json(
                [
                    'status' => Response::HTTP_BAD_REQUEST,
                    'message' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        } catch (NotNormalizableValueException $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
