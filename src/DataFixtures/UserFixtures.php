<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $om): void
    {
        foreach ([
                     ['user@example.com', 'password', ['ROLE_USER']],
                     ['admin@example.com', 'adminpass', ['ROLE_ADMIN']],
                 ] as [$email, $plain, $roles]) {
            $u = (new User())->setEmail($email)->setRoles($roles);
            $u->setPassword($this->hasher->hashPassword($u, $plain));
            $om->persist($u);
        }
        $om->flush();
    }
}
