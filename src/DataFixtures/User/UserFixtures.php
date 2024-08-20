<?php

namespace App\DataFixtures\User;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use JsonException;

class UserFixtures extends Fixture
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadClients($manager);

    }

    /**
     * @param ObjectManager $manager
     * @throws JsonException
     * @throws Exception
     */
    private function loadUsers(ObjectManager $manager): void
    {
        $userFileDir = __DIR__ . '/users.json';
        $fixtureUser = json_decode(
            file_get_contents($userFileDir),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        foreach ($fixtureUser as $user) {
            $entity = new User();

            $entity->setEmail($user['email']);
            $entity->setPassword($user['password']);
            $entity->setRoles($user['roles']);

            $manager->persist($entity);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @throws JsonException
     * @throws Exception
     */
    private function loadClients(ObjectManager $manager): void
    {
        $clientFileDir = __DIR__ . '/clients.json';
        $fixtureClient = json_decode(
            file_get_contents($clientFileDir),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        foreach ($fixtureClient as $client){
            $entity = new Client();

            $entity->setAddress($client['address'] ?? null);
            $entity->setPhoneNumber($client['phoneNumber'] ?? null);
            $entity->setBirthday($client['birthday'] ? new DateTime($client['birthday']) : null);
            $entity->setUser($this->userRepository->findOneBy(['email' => $client['userEmail']]));

            $manager->persist($entity);
        }
        $manager->flush();
    }
}