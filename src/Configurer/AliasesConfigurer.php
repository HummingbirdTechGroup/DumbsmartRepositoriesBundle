<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException;
use carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\Metadata\AliasedMetadataFactory;

class AliasesConfigurer
{
    /**
     * @var MetadataManager
     */
    private $mm;

    /**
     * @var RepositoryManager
     */
    private $rm;

    /**
     * @var AliasedMetadataFactory
     */
    private $amf;

    /**
     * @param MetadataManager        $mm
     * @param RepositoryManager      $rm
     * @param AliasedMetadataFactory $amf
     */
    public function __construct(
        MetadataManager $mm,
        RepositoryManager $rm,
        AliasedMetadataFactory $amf
    ) {
        $this->mm = $mm;
        $this->rm = $rm;
        $this->amf = $amf;
    }

    /**
     * @param array $aliases
     *
     * @throws MetadataNotFoundException
     * @throws RepositoryNotFoundException
     */
    public function configure(array $aliases)
    {
        foreach ($aliases as $alias => $config) {
            $this->mm->getMetadataForClassName($config['class'])->getObjectIdentifier()->setAlias($alias);

            $this->mm->addMetadata($alias, $this->amf->createMetadata($config));
            $this->rm->addRepository($alias, $this->rm->getRepositoryForClassName($config['class']));
        }
    }

}
