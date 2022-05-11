<?php

namespace App\Controller\Api;

use App\Entity\Product;
use OpenApi\Annotations as OA;
use App\Repository\ProductRepository;
use App\Services\PaginationService;
use Doctrine\Persistence\ManagerRegistry;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
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
     * ShowProductsList
     * Return list of product in a json response
     * 
     * @Route("/api/products", name="api_product_list", methods={"GET"})
     * @OA\Get(
     *      description="List of products",
     *      tags={"Product"},
     *      @OA\Parameter(ref="#/components/parameters/page"),
     *      @OA\Parameter(ref="#/components/parameters/limit"),
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
     *          ),
     *          @OA\Link(
     *              link="ShowProduct",
     *              description="Show details of a product<br/>The <code>id</code> value returned in the response can be used as the id parameter in<br/><code>GET /api/products/{id}</code>.",
     *              operationId="showProduct",
     *              parameters= {
     *                  {
     *                      "name": "id",
     *                      "description": "Product's id",
     *                      "required": true,
     *                      "type": "integer",
     *                      "paramType": "path",
     *                      "allowMultiple": false
     *                  }
     *              },
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          ref="#/components/responses/401"
     *      )
     * )
     *
     * @param  ProductRepository $repoProduct
     * @param  PaginationService $pagination
     * @param  Request $request
     * @return Response
     */
    public function showProductsList(
        PaginationService $pagination,
        ProductRepository $repoProduct,
        Request $request
    ): Response {
        $json = $this->json(
            $repoProduct->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => 'list_product']
        );

        if ($pagination->verifInteger($request->get('page'))) {
            $page = $request->get('page');
            $limit = 10;
            if ($pagination->verifInteger($request->get('limit'))) {
                $limit = $request->query->get('limit');
            }
            $json = $this->json(
                $repoProduct->findBy(
                    [],
                    ['id' => 'ASC'],
                    $limit,
                    $page * $limit
                ),
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
     *      path="/api/products/{id}",
     *      description="List the characteristics of the specified product.",
     *      tags={"Product"},
     *      @OA\Parameter(ref="#/components/parameters/id"),
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
     *          ref="#/components/responses/401"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          ref="#/components/responses/404"
     *      ),
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
                'message' => 'The desired resource was not found'
            ],
            Response::HTTP_NOT_FOUND
        );
    }
}
