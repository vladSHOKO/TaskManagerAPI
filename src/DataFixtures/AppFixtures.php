<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->createOne([
            'email' => 'test@test.ru',
            'plainPassword' => 'pass'
        ]);

        UserFactory::new()->createMany(10);

        for ($i = 0; $i < 20; $i++) {
            TaskFactory::new()->createOne(['owner' => UserFactory::random()]);
        }
    }
}
