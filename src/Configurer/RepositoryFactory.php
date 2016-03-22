<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Configurer;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\FileRepository;
use Everzet\PersistedObjects\InMemoryRepository;
use Everzet\PersistedObjects\ObjectIdentifier;
use Everzet\PersistedObjects\Repository;

class RepositoryFactory
{
    const TYPE_IN_MEMORY = 'in_memory';
    const TYPE_FILE = 'file';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $type
     * @param string $path
     */
    public function __construct($type, $path)
    {
        $this->type = $type;
        $this->path = $path;
    }

    /**
     * @param ClassMetadata    $metadata
     * @param ObjectIdentifier $identifier
     *
     * @return Repository
     */
    public function createRepository(ClassMetadata $metadata, ObjectIdentifier $identifier)
    {
        switch ($this->type) {
            case self::TYPE_FILE:
                return new FileRepository($this->getFileName($metadata), $identifier);

            default:
                return new InMemoryRepository($identifier);
        }
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getFileName(ClassMetadata $metadata)
    {
        return sprintf(
            '%s%s%s.repository',
            rtrim($this->path, DIRECTORY_SEPARATOR),
            DIRECTORY_SEPARATOR,
            str_replace('\\', '_', $metadata->getName())
        );
    }
}
