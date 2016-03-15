<?php

namespace carlosV2\DumbsmartRepositoriesBundle\RepositoryFactories;

use carlosV2\DumbsmartRepositoriesBundle\DoctrineObjectIdentifier;
use carlosV2\DumbsmartRepositoriesBundle\RepositoryFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\FileRepository;

class FileRepositoryFactory implements RepositoryFactory
{
    const TYPE = 'file';

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function createRepository(ClassMetadata $metadata)
    {
        return new FileRepository($this->getFileName($metadata), new DoctrineObjectIdentifier($metadata));
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

    /**
     * {@inheritdoc}
     */
    public function supports($type)
    {
        return $type === self::TYPE;
    }
}
