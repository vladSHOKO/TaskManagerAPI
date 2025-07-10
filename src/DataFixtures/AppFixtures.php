<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\TokenFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->createOne([
            'email' => 'test@admin.ru',
            'plainPassword' => 'pass',
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]);

        UserFactory::new()->createOne([
            'email' => 'test@user.ru',
            'plainPassword' => 'pass',
            'roles' => ['ROLE_USER'],
        ]);

        UserFactory::new()->createMany(10);

        for ($i = 0; $i < 20; $i++) {
            TokenFactory::new()->createOne(['owner' => UserFactory::random()]);
        }

        for ($i = 0; $i < 30; $i++) {
            TaskFactory::new()->createOne(['owner' => UserFactory::random()]);
        }
    }
}
