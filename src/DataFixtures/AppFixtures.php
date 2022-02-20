<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un administrateur
        $admin = new User();
        $admin->setNom('ADMIN');
        $admin->setPrenom('Role');
        $admin->setEmail('admin@mail.fr');
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Création d'un super administrateur
        $sAdmin = new User();
        $sAdmin->setNom('SUPER ADMIN');
        $sAdmin->setPrenom('Role');
        $sAdmin->setEmail('super.admin@mail.fr');
        $sAdmin->setRoles(["ROLE_SUPER_ADMIN"]);
        $sAdmin->setPassword($this->hasher->hashPassword($sAdmin, 'admin123'));
        $manager->persist($sAdmin);

        $manager->flush();
    }
}
