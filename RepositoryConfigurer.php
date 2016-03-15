<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\RepositoryManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

class RepositoryConfigurer
{
    /**
     * @var RepositoryManager
     */
    private $manager;

    /**
     * @var RepositoryFactory[]
     */
    private $factories;

    /**
     * @var string
     */
    private $type;

    /**
     * @param RepositoryManager $manager
     * @param string            $type
     */
    public function __construct(RepositoryManager $manager, $type)
    {
        $this->manager = $manager;
        $this->type = $type;
        $this->factories = [];
    }

    /**
     * @param RepositoryFactory $factory
     */
    public function addRepositoryFactory(RepositoryFactory $factory)
    {
        $this->factories[] = $factory;
    }

    /**
     * @param ClassMetadataFactory $factory
     */
    public function configureRepositories(ClassMetadataFactory $factory)
    {
        foreach ($factory->getAllMetadata() as $metadata) {
            $this->manager->addRepository(
                $metadata->getName(),
                $this->getRepositoryFactory()->createRepository($metadata)
            );
        }
    }

    /**
     * @return RepositoryFactory
     */
    private function getRepositoryFactory()
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($this->type)) {
                return $factory;
            }
        }

        throw new \InvalidArgumentException('RepositoryFactory not found for type `' . $this->type .'`.');
    }
}
