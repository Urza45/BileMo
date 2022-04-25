<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setTitle('Samsung Galaxy S20 FE');
        $product->setDescription('Grand écran 6,5’’ ultra fluide 120Hz
        Des photos lumineuses de jour et de nuit
        Space Zoom 30x
        128 Go de stockage et port microSD
        Prêt pour le futur en 5G');
        $product->setColor('Bleu');
        $product->setPrice(499.00);

        $manager->persist($product);

        $product = new Product();
        $product->setTitle('Doro 1360 noir');
        $product->setDescription('Ecran 2.4" - 240x320 pixels
        Slot micro SD
        Bluetooth - Radio FM
        Double SIM');
        $product->setColor('Noir');
        $product->setPrice(44.90);

        $manager->persist($product);

        $product = new Product();
        $product->setTitle('Oppo A16 Noir 64Go');
        $product->setDescription('OS ColorOS 11.1 basé sur Android 11 - 64 Go de ROM, 4Go de RAM
        Ecran de 6,52" (1600x720 HD+) - 60Hz
        Processeur MediaTek Helio G35
        Triple caméra avec Intelligence Artificielle 13Mp');
        $product->setColor('Noir');
        $product->setPrice(179.00);

        $manager->persist($product);

        $product = new Product();
        $product->setTitle('Doro 6060 rouge');
        $product->setDescription('Mobile sous OS propriétaire
        Ecran 2,8" 320x240QVGA
        Processeur MT6260A
        Appareil photo 3 MP');
        $product->setColor('Rouge');
        $product->setPrice(89,90);

        $manager->persist($product);


        $manager->flush();
    }
}
