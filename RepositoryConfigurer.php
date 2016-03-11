<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\RepositoryManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Everzet\PersistedObjects\FileRepository;
use Everzet\PersistedObjects\InMemoryRepository;
use Everzet\PersistedObjects\Repository;

class RepositoryConfigurer
{
    const TYPE_IN_MEMORY = 'in_memory';
    const TYPE_FILE = 'file';

    /**
     * @var RepositoryManager
     */
    private $manager;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string|null
     */
    private $path;

    /**
     * @param RepositoryManager $manager
     * @param string            $type
     * @param string|null       $path
     */
    public function __construct(RepositoryManager $manager, $type, $path = null)
    {
        $this->manager = $manager;
        $this->type = $type;
        $this->path = $path;
    }

    /**
     * @param ClassMetadataFactory $factory
     */
    public function configureRepositories(ClassMetadataFactory $factory)
    {
        foreach ($factory->getAllMetadata() as $metadata) {
            $this->manager->addRepository($metadata->getName(), $this->createRepository($metadata));
        }
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return Repository
     *
     * @throws InvalidArgumentException
     */
    private function createRepository(ClassMetadata $metadata)
    {
        switch ($this->type) {
            case self::TYPE_IN_MEMORY:
                return $this->createInMemoryRepository($metadata);
                break;

            case self::TYPE_FILE:
                return $this->createFileRepository($metadata, $this->path);
                break;

            default:
                throw new InvalidArgumentException();
        }
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return InMemoryRepository
     */
    private function createInMemoryRepository(ClassMetadata $metadata)
    {
        return new InMemoryRepository($this->createObjectIdentifier($metadata));
    }

    /**
     * @param ClassMetadata $metadata
     * @param string        $path
     *
     * @return FileRepository
     */
    private function createFileRepository(ClassMetadata $metadata, $path)
    {
        $filename = rtrim($path, '/') . '/' . md5($metadata->getName());

        return new FileRepository($filename, $this->createObjectIdentifier($metadata));
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return DoctrineObjectIdentifier
     */
    private function createObjectIdentifier(ClassMetadata $metadata)
    {
        return new DoctrineObjectIdentifier($metadata);
    }
}
