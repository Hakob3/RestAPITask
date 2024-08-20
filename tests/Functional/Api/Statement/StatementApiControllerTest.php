<?php

namespace App\Tests\Functional\Api\Statement;

use App\Entity\Statement;
use App\Tests\Functional\Api\AbstractApiCase;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class StatementApiControllerTest extends AbstractApiCase
{
    /**
     * @group test
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testGetStatements(): void
    {
        $this->createStatements();
        $this->em->flush();

        $response = $this->apiRequest(
            $this->router->generate('api_statement_list'),
            'GET'
        );
        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('items', $content);
        $this->assertNotCount(0, $content['items']);
    }

    /**
     * @dataProvider singleStatement
     * @group test
     * @param bool $found
     * @param int $statusCode
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testGetSingleStatement(
        bool $found,
        int  $statusCode
    ): void
    {
        $this->createStatements();
        $this->em->flush();
        $statements = $this->em->getRepository(Statement::class)->findAll();

        $response = $this->apiRequest(
            $this->router->generate('api_statement_single', ['id' => $found ? $statements[0]->getId() : 0]),
            'GET'
        );
        $content = json_decode($response->getContent(), true);
        $this->assertEquals($statusCode, $response->getStatusCode());
        if ($found) {
            $this->assertArrayHasKey('item', $content);
        }
    }

    public function singleStatement(): array
    {
        return [
            'getExistStatement' => [
                'found' => true,
                'statusCode' => 200
            ],
            'getNotExistStatement' => [
                'found' => false,
                'statusCode' => 404,
            ],
        ];
    }
}