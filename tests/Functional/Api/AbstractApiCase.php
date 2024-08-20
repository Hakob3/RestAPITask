<?php

namespace App\Tests\Functional\Api;

use App\Entity\Client;
use App\Entity\Statement;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractApiCase extends WebTestCase
{
    protected const
        EMAIL = "mail@mail23.ru",
        ROLE_USER = User::ROLE_USER,
        ROLE_CLIENT = User::ROLE_CLIENT,
        CLIENT_EMAIL = "client@user.ru",
        PASSWORD = "Fg3i@saur43";

    protected const STATEMENTS = [
        [
            'title' => 'Title1',
            'content' => 'Some content 1',
            'clientEmail' => 'client1@user.ru'
        ],
        [
            'title' => 'Title2',
            'content' => 'Some content 1',
            'clientEmail' => 'client2@user.ru'
        ]
    ];


    /** @var KernelBrowser|null */
    protected KernelBrowser|null $client = null;

    /** @var RouterInterface|null */
    protected RouterInterface|null $router = null;

    protected EntityManager|null $em = null;

    protected string $projectDir;


    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->client->disableReboot();

        $this->iniDi();
    }

    public function iniDi(): void
    {
        $this->router = static::getContainer()->get(RouterInterface::class);
        $this->em = static::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->projectDir = static::getContainer()->get('kernel')->getProjectDir();

        $purger = new ORMPurger($this->em);
        $purger->purge();
    }

    public function tearDown(): void
    {
        parent::teardown();
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $parameters
     * @param array $files
     * @param array $headers
     * @param string|null $content
     * @return Response
     */
    public function apiRequest(
        string $url,
        string $method = Request::METHOD_GET,
        array  $parameters = [],
        array  $files = [],
        array  $headers = [],
        string $content = null
    ): Response
    {
        $this->client->request($method, $url, $parameters, $files, $headers, $content);

        return $this->client->getResponse();
    }

    /**
     * @param string $email
     * @return User|null
     */
    protected function getUser(string $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy([
            "email" => $email,
        ]);
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $role
     * @return User
     * @throws ORMException
     */
    protected function createUser(
        string $email = self::EMAIL,
        string $password = self::PASSWORD,
        string $role = self::ROLE_USER,
    ): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$role]);
        $user->setPassword($password);
        $this->em->persist($user);

        return $user;
    }

    /**
     * @param string $email
     * @return Client
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function createClientUser(string $email = self::CLIENT_EMAIL): Client
    {
        $client = new Client();
        $user = $this->createUser(email: $email,  role: User::ROLE_CLIENT);

        $client->setUser($user);
        $this->em->persist($client);

        return $client;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function createStatements(): void
    {
        foreach (self::STATEMENTS as $statement){
            $statementEntity = new Statement();
            $statementEntity->setTitle($statement['title']);
            $statementEntity->setContent($statement['content']);
            $statementEntity->setClient($this->createClientUser($statement['clientEmail']));
            $this->em->persist($statementEntity);
        }
    }

    protected function getAuthToken(string $email, string $password, int $responseStatusCode = 200): string
    {
        $url = $this->router->generate('api_login_check');

        $response = $this->apiRequest(
            $url,
            Request::METHOD_POST,
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: json_encode(
                [
                    "email" => $email,
                    "password" => $password,
                ]
            )
        );
        $this->assertEquals($responseStatusCode, $response->getStatusCode());

        if (Response::HTTP_OK === $responseStatusCode) {
            $content = json_decode($response->getContent(), true);

            $this->assertArrayHasKey('token', $content);

            return $content['token'];
        }

        return 'wrong';
    }
}
