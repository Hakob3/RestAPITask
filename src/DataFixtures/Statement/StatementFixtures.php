<?php

namespace App\DataFixtures\Statement;

use App\DataFixtures\User\UserFixtures;
use App\Entity\Statement;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StatementFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    /**
     * @return class-string[]
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $statementFileDir = __DIR__ . '/statements.json';
        $fixtureStatements = json_decode(
            file_get_contents($statementFileDir),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        foreach ($fixtureStatements as $statement) {
            $entity = new Statement();
            $entity->setTitle($statement['title']);
            $entity->setContent($statement['content']);

            $filename = $statement['attachmentFile'] ?? null;

            if ($filename) {
                $filesystem = new Filesystem();
                $tempFilePath = sys_get_temp_dir() . '/' . $filename;

                $filesystem->copy(__DIR__ . '/' . $filename, $tempFilePath, true);
                $file = new UploadedFile($tempFilePath, $filename, null, null, true);
                $entity->setAttachmentFile($file);
            }

            $entity->setClient(
                $this->userRepository->findOneBy(
                    [
                        'email' => $statement['userEmail']
                    ]
                )->getClient()
            );

            $manager->persist($entity);
        }
        $manager->flush();
    }
}