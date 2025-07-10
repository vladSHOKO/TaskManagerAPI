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
            'email' => 'test@test.ru',
            'plainPassword' => 'pass',
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]);

        UserFactory::new()->createMany(10);

        for ($i = 0; $i < 20; $i++) {
            TokenFactory::new()->createOne(['owner' => UserFactory::random()]);
        }

        for ($i = 0; $i < 20; $i++) {
            TaskFactory::new()->createOne(['owner' => UserFactory::random()]);
        }
    }
}
