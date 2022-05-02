<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $client = new Client();
        $client->setName('Société Martin & Fils');
        $client->setEmail('martin@email.com');
        $client->setPassword('$2y$13$5KOrZ26XdAsF1q/5PUh8lO6kteMS6q6r74tkqATjec8uB3gTcQvNm');
        $client->setRoles(['ROLE_USER']);

        $manager->persist($client);

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setClient($client)
                ->setFirstName('User' . $i)
                ->setLastName('Martin')
                ->setEmail('user' . $i . '.martin@email.com')
                ->setAddress('23, Rue des Glicines')
                ->setPostalCode('01000')
                ->setCity('Bourg-en-Bresse');
            $manager->persist($user);
        }

        $client = new Client();
        $client->setName('Société Landru & Company');
        $client->setEmail('landru@email.com');
        $client->setPassword('$2y$13$PVnky4kZ5ispTaKVZaEDseXEyZe3NdiUU8gDlrZlE9qf9fy2PcuoK');
        $client->setRoles(['ROLE_USER']);

        $manager->persist($client);

        for ($i = 5; $i < 10; $i++) {
            $user = new User();
            $user->setClient($client)
                ->setFirstName('User' . $i)
                ->setLastName('landru')
                ->setEmail('user' . $i . '.landru@email.com')
                ->setAddress('23, Rue des Glicines')
                ->setPostalCode('36340')
                ->setCity('Cluis');
            $manager->persist($user);
        }

        $manager->flush();
    }
}
