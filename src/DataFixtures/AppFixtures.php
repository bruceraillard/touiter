<?php

namespace App\DataFixtures;

use App\Entity\Touit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $om): void
    {
        foreach ([
                     ['Bienvenue sur Touiter !', 'Admin'],
                     ['Symfony 7.3 + JWT, let’s go', 'Alice'],
                     ['160 chars max 👍', 'Bob'],
                 ] as [$c, $a]) {
            $t = (new Touit())
                ->setContenu($c)
                ->setAuthor($a)
                ->setCreatedAt(new \DateTimeImmutable());

            $om->persist($t);
        }
        $om->flush();
    }
}
