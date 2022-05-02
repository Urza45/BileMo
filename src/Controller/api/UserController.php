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

class UserController extends AbstractController
{
    /**
     * ShowUserList
     * Return list of users in a json response
     * 
     * @Route("/api/users", name="app_user_list", methods={"GET"})
     *
     * @param  ClientRepository $repoClient
     * @param  Request $request
     * @return Response
     */
    public function showUserList(ClientRepository $repoClient, Request $request): Response
    {
        $client = $repoClient->findOneBy(['id' => $this->getUser()->getId()]);

        return $this->json(
            $client->getUsers(),
            Response::HTTP_OK,
            [],
            ['groups' => 'list_user']
        );
    }

    /**
     * ShowUserList
     * Retourne un utilisateur associé à un client
     * 
     * @Route("/api/users/{id}", name="app_user_show", methods={"GET"})
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
                        'status' => Response::HTTP_FORBIDDEN,
                        'message' => 'Vous n\'avez pas accès aux informations de cet utilisateur'
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }
            return $this->json($user, Response::HTTP_OK, [], ['groups' => 'show_user']);
        }

        return $this->json(
            [
                'status' => Response::HTTP_NOT_FOUND,
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
     * @param  ManagerRegistry $doctrine
     * @param  SerializerInterface $serializer
     * @param  Request $request
     * @param  ValidatorInterface $validator
     * @param  ClientRepository $repoClient
     * @return Response
     */
    public function addUser(ManagerRegistry $doctrine, SerializerInterface $serializer, ValidatorInterface $validator, ClientRepository $repoClient, Request $request): Response
    {
        $client = $repoClient->findOneBy(['id' => $request->get('index')]);
        $data = $request->getContent();

        try {
            $user = $serializer->deserialize($data, User::class, 'json');

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $user->setClient($client);
            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->json(
                $user,
                Response::HTTP_CREATED,
                [],
                ['groups' => 'show_user']
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

    /**
     * DeleteUser
     * 
     * @Route("/api/users/{id}", name="app_user_delete", methods={"DELETE"})
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
                        'status' => Response::HTTP_FORBIDDEN,
                        'message' => 'Vous n\avez pas accès à cet utilisateur'
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }
            $repoUSer->remove($user, true);

            return $this->json(
                [
                    'Status' => Response::HTTP_OK,
                    'message' => 'L\'utilisateur a bien été supprimé.'
                ],
                Response::HTTP_OK
            );
        }

        return $this->json(
            [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'L\'utilisateur recherché n\'a pas été trouvé'
            ],
            Response::HTTP_NOT_FOUND
        );
    }
}
