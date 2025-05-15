<?php

namespace App\DataFixtures;

use App\Entity\Tweet;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();

        $users = [];

        // Créer une dizaine d'utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername('user' . $i);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'password' . $i));
            $user->setCreatdAt($faker->dateTimeBetween('-1 year', 'now'));

            $manager->persist($user);
            $users[] = $user;
        }

        // Créer une centaine de tweets
        for ($i = 0; $i < 100; $i++) {
            $tweet = new Tweet();
            $tweet->setContent(implode(' ', $faker->words(10)));
            $tweet->setUsr($users[array_rand($users)]); // Utilisateur aléatoire
            $tweet->setCreatedAt($faker->dateTimeBetween('-1 year', 'now'));

            // Ajout de likes aléatoires (5 à 10 utilisateurs différents)
            $likeIndices = array_rand($users, rand(5, 10));
            if (!is_array($likeIndices)) {
                $likeIndices = [$likeIndices];
            }
            foreach ($likeIndices as $index) {
                $tweet->addLike($users[$index]);
            }

            $manager->persist($tweet);
        }

        $manager->flush();
    }

}