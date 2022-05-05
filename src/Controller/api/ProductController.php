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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * ProductController
 */
class ProductController extends AbstractController
{
    /**
     * ShowProductsList
     * Return list of product in a json response
     * 
     * @Route("/api/products", name="api_product_list", methods={"GET"})
     * 
     * @OA\Get(
     *      description="List of products",
     *      tags={"Product"},
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="Current page of product list.",
     *          @OA\Schema(type="string"),
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          required=false,
     *          description="Maximum number of products per page.",
     *          @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Returns the list of products",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer", example=200),
     *                  @OA\Property(property="message", type="string", example="OK"),
     *                  @OA\Property(
     *                      property="products", 
     *                      type="array",
     *                      @OA\Items(ref=@Model(type=Product::class, groups={"list_product"})) 
     *                  ),
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Return problems with parameters (Expired token, no token).",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 401, "message": "JWT Token not found"},
     *              )
     *          )
     *      )
     * )
     *
     * @param  ProductRepository $repoProduct
     * @return Response
     */
    public function showProductsList(ProductRepository $repoProduct, Request $request): Response
    {
        if ($request->query->get('page') !== null) {
            $limit = 10;
            if ($request->query->get('limit') !== null) {
                $limit = $request->query->get('limit');
            }
            $json = $this->json(
                $repoProduct->findBy(
                    [],
                    ['id' => 'ASC'],
                    $limit,
                    $request->query->get('page') * $limit
                ),
                Response::HTTP_OK,
                [],
                ['groups' => 'list_product']
            );
        } else {
            $json = $this->json(
                $repoProduct->findAll(),
                Response::HTTP_OK,
                [],
                ['groups' => 'list_product']
            );
        }

        $jsonToArray = [
            "code" => 200,
            "message" => "OK",
            "products" => json_decode($json->getContent(), true)
        ];

        return $this->json($jsonToArray, Response::HTTP_OK);
    }

    /**
     * ShowProduct
     * Return a product in a json response
     * 
     * @Route("/api/products/{id}", name="api_product_show", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @OA\Get(
     *      description="List the characteristics of the specified product.",
     *      tags={"Product"},
     *      @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path",
     *          description="The product unique identifier.",
     *          @OA\Schema(type="integer"),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Returns informations of a specific product",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer", example=200),
     *                  @OA\Property(property="message", type="string", example="OK"),
     *                  @OA\Property(property="product", type="object", ref=@Model(type=Product::class, groups={"show_product"}) ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Return problems with parameters (Expired token, no token).",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 401, "message": "JWT Token not found"},
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="The desired product was not found.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 404, "message": "Le produit recherché n'a pas été trouvé"},
     *              )
     *          )
     *      )
     * )
     * 
     * @param  Product $product
     * @return Response
     */
    public function showProduct(Product $product = null): Response
    {
        if ($product) {
            $json = $this->json(
                $product,
                Response::HTTP_OK,
                [],
                ['groups' => 'show_product']
            );
            $jsonToArray = [
                "code" => 200,
                "message" => "OK",
                "product" => json_decode($json->getContent(), true)
            ];

            return $this->json($jsonToArray, Response::HTTP_OK);
        }

        return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Le produit recherché n\'a pas été trouvé'
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * AddProduct
     * Add a product from a json request
     * 
     * @Route("/api/products", name="api_product_add", methods={"POST"})
     * 
     * @OA\Post(
     *      description="List the characteristics of the specified client (Restricted to admin)",
     *      tags={"Product"},
     *      @OA\Response(
     *          response=200,
     *          description="Returns the rewards of an user",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Product::class, groups={"show_product"}))
     *          )
     *      )
     * )
     *
     * @param  ManagerRegistry $doctrine
     * @param  SerializerInterface $serializer
     * @param  Request $request
     * @param  ValidatorInterface $validator
     * @return Response
     */
    //     public function addProduct(ManagerRegistry $doctrine, SerializerInterface $serializer, Request $request, ValidatorInterface $validator): Response
    //     {
    //         $data = $request->getContent();
    //         $data2 = json_decode($data, true);
    //         //dd((float) $data2['price']);
    //         $data2['price'] = (float) $data2['price'];

    //         $data = json_encode($data2);

    //         try {
    //             $product = $serializer->deserialize($data, Product::class, 'json');

    //             $errors = $validator->validate($product);

    //             if (count($errors) > 0) {
    //                 return $this->json($errors, Response::HTTP_BAD_REQUEST);
    //             }

    //             $manager = $doctrine->getManager();
    //             $manager->persist($product);
    //             $manager->flush();

    //             return $this->json(
    //                 $product,
    //                 Response::HTTP_CREATED,
    //                 [],
    //                 ['groups' => 'show_product']
    //             );
    //         } catch (NotEncodableValueException $e) {
    //             return $this->json(
    //                 [
    //                     'status' => Response::HTTP_BAD_REQUEST,
    //                     'message' => $e->getMessage()
    //                 ],
    //                 Response::HTTP_BAD_REQUEST
    //             );
    //         } //catch (NotNormalizableValueException $e) {
    //         //     return $this->json([
    //         //         'status' => Response::HTTP_BAD_REQUEST,
    //         //         'message' => 'Erreur de type de données' //$e->getMessage(),
    //         //     ], Response::HTTP_BAD_REQUEST);
    //         // }
    //     }
}
