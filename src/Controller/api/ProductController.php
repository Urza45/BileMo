<?php

namespace App\Controller\api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Container64kdti8\getProductControllerService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
     * Return a product in a json response
     * 
     * @Route("/api/products/{id}", name="api_product_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    // public function showProduct(ProductRepository $repoProduct, Request $request): Response
    public function showProduct(Product $product = null, Request $request): Response
    {
        //$product = $repoProduct->findOneBy(['id' => $request->get('index')]);

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
     * Add a product from a json request
     * 
     * @Route("/api/products", name="api_product_add", methods={"POST"})
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
            dd($e);
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
