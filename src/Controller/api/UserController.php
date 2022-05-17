<?php

namespace App\Controller\Api;

use App\Entity\User;
use OpenApi\Annotations as OA;
use App\Repository\UserRepository;
use App\Services\PaginationService;
use App\Repository\ClientRepository;
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

class UserController extends AbstractController
{
    /**
     * ShowUserList
     * Return list of users in a json response
     * 
     * @Route("/api/users", name="app_user_list", methods={"GET"})
     * 
     * @OA\Get(
     *      description="Returns the list of users associated with a client",
     *      tags={"User"},
     *      @OA\Parameter(ref="#/components/parameters/page"),
     *      @OA\Parameter(ref="#/components/parameters/limit"),
     *      @OA\Response(
     *          response=200,
     *          description="Returns the list of users",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer", example=200),
     *                  @OA\Property(property="message", type="string", example="OK"),
     *                  @OA\Property(
     *                      property="users", 
     *                      type="array",
     *                      @OA\Items(ref=@Model(type=User::class, groups={"list_user"})) 
     *                  ),
     *              ),
     *          ),
     *          @OA\Link(
     *              link="ShowUser",
     *              description="Show details of a user<br/>The <code>id</code> value returned in the response can be used as the id parameter in<br/><code>GET /api/users/{id}</code>.",
     *              operationId="showUser",
     *              parameters= {
     *                  {
     *                      "name": "id",
     *                      "description": "User's id",
     *                      "required": true,
     *                      "type": "integer",
     *                      "paramType": "path",
     *                      "allowMultiple": false
     *                  },
     *              },
     *          ),
     *          @OA\Link(
     *              link="DeleteUser",
     *              description="Delete a user<br/>The <code>id</code> value returned in the response can be used as the id parameter in<br/><code>DELETE /api/users/{id}</code>",
     *              operationId="deleteUser",
     *              parameters= {
     *                  {
     *                      "name": "id",
     *                      "description": "User's id",
     *                      "required": true,
     *                      "type": "integer",
     *                      "paramType": "path",
     *                      "allowMultiple": false
     *                  },
     *              },
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          ref="#/components/responses/401"
     *      )
     * )
     * 
     * @param  ClientRepository $repoClient
     * @param  PaginationService $pagination
     * @param  Request $request
     * @return Response
     */
    public function showUserList(
        PaginationService $pagination,
        ClientRepository $repoClient,
        Request $request
    ): Response {
        $client = $repoClient->findOneBy(['id' => $this->getUser()->getId()]);

        $json = $this->json(
            $client->getUsers(),
            Response::HTTP_OK,
            [], // Empty header
            ['groups' => 'list_user']
        );

        if ($pagination->verifInteger($request->get('page'))) {
            $page = $request->get('page');
            $limit = PaginationService::LIMIT_DEFAULT;
            if ($pagination->verifInteger($request->get('limit'))) {
                $limit = $request->query->get('limit');
            }

            $contentJson = (array) json_decode($json->getContent());

            $json = $this->json(
                array_slice($contentJson, $page * $limit, $limit),
                Response::HTTP_OK,
            );
        }

        $jsonToArray = [
            "code" => 200,
            "message" => "OK",
            "users" => json_decode($json->getContent(), true)
        ];

        return $this->json($jsonToArray, Response::HTTP_OK);
    }

    /**
     * ShowUser
     * Returns a user associated with a client
     * 
     * @Route("/api/users/{id}", name="app_user_show", methods={"GET"})
     * 
     * @OA\Get(
     *      path="/api/users/{id}",
     *      operationId="showUser",
     *      description="List the characteristics of a specified user",
     *      tags={"User"},
     *      @OA\Parameter(
     *          name="id",
     *          ref="#/components/parameters/id"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Returns the characteristics of a specified user",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer", example=200),
     *                  @OA\Property(property="message", type="string", example="OK"),
     *                  @OA\Property(property="user", type="object", ref=@Model(type=User::class, groups={"show_user"}) ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          ref="#/components/responses/401"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          ref="#/components/responses/403"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          ref="#/components/responses/404"
     *      ),   
     * )
     *
     * @param  User $user
     * @param  Request $request
     * @return Response
     */
    public function showUser(User $user = null, Request $request): Response
    {
        if ($user) {
            if ($user->getClient()->getId() != $this->getUser()->getId()) {
                return $this->json(
                    [
                        'code' => Response::HTTP_FORBIDDEN,
                        'message' => 'The desired resource is not authorized.'
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }

            $json = $this->json(
                $user,
                Response::HTTP_OK,
                [], // Empty header
                ['groups' => 'show_user']
            );

            $jsonToArray = [
                "code" => 200,
                "message" => "OK",
                "users" => json_decode($json->getContent(), true)
            ];

            return $this->json($jsonToArray, Response::HTTP_OK);

            // return $this->json($user, Response::HTTP_OK, [], ['groups' => 'show_user']);
        }

        return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'The desired resource was not found'
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * addUser
     * 
     * @Route("/api/users", name="app_user_add", methods={"POST"})
     * 
     * @OA\Post(
     *      description="Allow an authenticated client to create a new user",
     *      tags={"User"},
     *      operationId="addUser",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="firstName", type="string", example="John"),
     *              @OA\Property(property="lastName", type="string", example="Doe"),
     *              @OA\Property(property="email", type="string", example="doe.martin@email.com"),
     *              @OA\Property(property="address", type="string", example="13 rue du Bois Blanc"),
     *              @OA\Property(property="postalCode", type="string", example="41000"),
     *              @OA\Property(property="city", type="string", example="Blois"),
     *              
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Returns informations of the new user",
     *          @OA\JsonContent(
     *              type="array",            
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer", example=201),
     *                  @OA\Property(property="message", type="string", example="Created."),
     *                  @OA\Property(property="user", type="object", ref=@Model(type=user::class, groups={"show_user","list_user"}) ),
     *              ),
     *          ),
     *          @OA\Link(
     *              link="ShowUser",
     *              description="Show details of a user<br/>The <code>id</code> value returned in the response can be used as the id parameter in<br/><code>GET /api/users/{id}</code>.",
     *              operationId="showUser",
     *              parameters= {
     *                  {
     *                      "name": "id",
     *                      "description": "User's id",
     *                      "required": true,
     *                      "type": "integer",
     *                      "paramType": "path",
     *                      "allowMultiple": false
     *                  },
     *              },
     *          ),
     *          @OA\Link(
     *              link="DeleteUser",
     *              description="Delete a user<br/>The <code>id</code> value returned in the response can be used as the id parameter in<br/><code>DELETE /api/users/{id}</code>",
     *              operationId="deleteUser",
     *              parameters= {
     *                  {
     *                      "name": "id",
     *                      "description": "User's id",
     *                      "required": true,
     *                      "type": "integer",
     *                      "paramType": "path",
     *                      "allowMultiple": false
     *                  }
     *              },
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          ref="#/components/responses/400"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          ref="#/components/responses/401"
     *      ),
     *       @OA\Response(
     *          response=409,
     *          ref="#/components/responses/409"
     *      ),     
     * )
     *
     * @param  ManagerRegistry $doctrine
     * @param  SerializerInterface $serializer
     * @param  Request $request
     * @param  ValidatorInterface $validator
     * @param  ClientRepository $repoClient
     * @param  UserRepository $repoUSer
     * @return Response
     */
    public function addUser(
        ManagerRegistry $doctrine,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ClientRepository $repoClient,
        Request $request,
        UserRepository $repoUser
    ): Response {
        $client = $repoClient->findOneBy(['id' => $request->get('index')]);
        $data = $request->getContent();

        // $user = $repoUser->findOneBy( ['email' => $data->get );

        try {
            // Verification of existing user
            $user = $serializer->deserialize($data, User::class, 'json');

            if ($repoUser->findOneBy(['email' => $user->getEmail()])) {
                $jsonToArray = [
                    "code" => 409,
                    "message" => "Duplicate resource detected.",
                ];
                return $this->json($jsonToArray, Response::HTTP_CONFLICT);
            }

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                $jsonToArray = [
                    "code" => 400,
                    "message" => "Bad request.",
                ];

                return $this->json($jsonToArray, Response::HTTP_BAD_REQUEST);
            }

            $user->setClient($client);
            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();

            $json = $this->json(
                $user,
                Response::HTTP_CREATED,
                [],
                ['groups' => ['show_user', 'list_user']]
            );

            $jsonToArray = [
                "code" => 201,
                "message" => "Created.",
                "user" => json_decode($json->getContent(), true)
            ];

            return $this->json($jsonToArray, Response::HTTP_CREATED);

            return $this->json(
                $user,
                Response::HTTP_CREATED,
                [],
                ['groups' => 'show_user']
            );
        } catch (NotEncodableValueException $e) {
            return $this->json(
                [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        } catch (NotNormalizableValueException $e) {
            return $this->json([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * DeleteUser
     * 
     * @Route("/api/users/{id}", name="app_user_delete", methods={"DELETE"})
     * 
     * @OA\Delete(
     *      description="Delete the targeted user",
     *      tags={"User"},
     *      operationId="deleteUser",
     *      @OA\Parameter(ref="#/components/parameters/id"),
     *      @OA\Response(
     *          response=204,
     *          description="Delete the targeted user",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          ref="#/components/responses/401"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          ref="#/components/responses/403"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          ref="#/components/responses/404"
     *      ) 
     * )
     *
     * @param  User $user
     * @param  Request $request
     * @param  UserRepository $repoUSer
     * @return Response
     */
    public function deleteUser(User $user = null, Request $request, UserRepository $repoUSer): Response
    {
        if ($user) {
            if ($user->getClient()->getId() != $this->getUser()->getId()) {
                return $this->json(
                    [
                        'code' => Response::HTTP_FORBIDDEN,
                        'message' => 'The desired resource is not authorized'
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }
            $repoUSer->remove($user, true);

            return $this->json(
                [
                    'code' => Response::HTTP_NO_CONTENT,
                ],
                Response::HTTP_NO_CONTENT
            );
        }

        return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'The desired resource was not found.'
            ],
            Response::HTTP_NOT_FOUND
        );
    }
}
