<?php

namespace App\DataFixtures;

use App\Entity\AdminUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixture extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $admin = new AdminUser();
        $admin->setEmail('admin@luxurybijoux.fr');
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin123@')
        );

        // If your AdminUser has roles property:
        if (method_exists($admin, 'setRoles')) {
            $admin->setRoles(['ROLE_ADMIN']);
        }

        $manager->persist($admin);
        $manager->flush();
    }
}