<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
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
     * @param  ClientRepository $repoClient
     * @param  Request $request
     * @return Response
     */
    public function showUserList(ClientRepository $repoClient, Request $request): Response
    {
        $client = $repoClient->findOneBy(['id' => $this->getUser()->getId()]);

        $json = $this->json(
            $client->getUsers(),
            Response::HTTP_OK,
            [],
            ['groups' => 'list_user']
        );

        $jsonToArray = [
            "code" => 200,
            "message" => "OK",
            "users" => json_decode($json->getContent(), true)
        ];

        return $this->json($jsonToArray, Response::HTTP_OK);
    }

    /**
     * ShowUser
     * Retourne un utilisateur associé à un client
     * 
     * @Route("/api/users/{id}", name="app_user_show", methods={"GET"})
     * 
     * @OA\Get(
     *      description="List the characteristics of the specified client (Restricted to admin)",
     *      tags={"User"},
     *      @OA\Response(
     *          response=200,
     *          description="Returns the rewards of an user",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer", example=200),
     *                  @OA\Property(property="message", type="string", example="OK"),
     *                  @OA\Property(property="user", type="object", ref=@Model(type=User::class, groups={"show_user"}) ),
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
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="The desired user is not authorized.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 403, "message": "Vous n'avez pas accès aux informations de cet utilisateur"},
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="The desired user was not found.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 404, "message": "L'utilisateur recherché n'a pas été trouvé"},
     *              )
     *          )
     *      )
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
                        'message' => 'Vous n\'avez pas accès aux informations de cet utilisateur'
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }
            return $this->json($user, Response::HTTP_OK, [], ['groups' => 'show_user']);
        }

        return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'L\'utilisateur recherché n\'a pas été trouvé'
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
     *          description="Returns informations of the new user of an user",
     *          @OA\JsonContent(
     *              type="array",            
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer", example=201),
     *                  @OA\Property(property="message", type="string", example="Utilisateur créé."),
     *                  @OA\Property(property="user", type="object", ref=@Model(type=user::class, groups={"show_user"}) ),
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Return problems with a paramter.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 400, "message": "Erreur sur un champ de donnée."},
     *              )
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
     *      ),
     *       @OA\Response(
     *          response=409,
     *          description="Duplicate user detected.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 409, "message": "L'utilisateur est déjà associé à ce client."},
     *              )
     *          )
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
        // Verification of existing user
        // $user = $repoUser->findOneBy( ['email' => $data->get );

        try {
            $user = $serializer->deserialize($data, User::class, 'json');

            if ($repoUser->findOneBy(['email' => $user->getEmail()])) {
                $jsonToArray = [
                    "code" => 409,
                    "message" => "L'utilisateur est déjà associé à ce client.",
                ];
                return $this->json($jsonToArray, Response::HTTP_CONFLICT);
            }

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                $jsonToArray = [
                    "code" => 400,
                    "message" => "Erreur sur un champ de donnée.",
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
                ['groups' => 'show_user']
            );

            $jsonToArray = [
                "code" => 201,
                "message" => "Utilisateur créé.",
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
     *      @OA\Response(
     *          response=200,
     *          description="Delete the targeted user",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 200, "message": "L'utilisateur a bien été supprimé."},
     *              )
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
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="The desired user is not authorized.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 403, "message": "Vous n'avez pas accès aux informations de cet utilisateur"},
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="The desired user was not found.",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="code", type="integer"),
     *                  @OA\Property(property="message", type="string"),
     *                  example={"code": 404, "message": "L'utilisateur recherché n'a pas été trouvé"},
     *              )
     *          )
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
                        'message' => 'Vous n\avez pas accès à cet utilisateur'
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }
            $repoUSer->remove($user, true);

            return $this->json(
                [
                    'code' => Response::HTTP_OK,
                    'message' => 'L\'utilisateur a bien été supprimé.'
                ],
                Response::HTTP_OK
            );
        }

        return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'L\'utilisateur recherché n\'a pas été trouvé'
            ],
            Response::HTTP_NOT_FOUND
        );
    }
}
