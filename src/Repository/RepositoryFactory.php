<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Repository;

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
     * @param string           $className
     * @param ObjectIdentifier $identifier
     *
     * @return Repository
     */
    public function createRepository($className, ObjectIdentifier $identifier)
    {
        switch ($this->type) {
            case self::TYPE_FILE:
                return new FileRepository($this->getFileName($className), $identifier);

            default:
                return new InMemoryRepository($identifier);
        }
    }

    /**
     * @param string $className
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getFileName($className)
    {
        return sprintf(
            '%s%s%s.repository',
            rtrim($this->path, DIRECTORY_SEPARATOR),
            DIRECTORY_SEPARATOR,
            str_replace('\\', '_', $className)
        );
    }
}
