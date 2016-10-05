<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException;
use carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\Metadata\DoctrineMetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier;
use carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\ObjectIdentifierFactory;
use carlosV2\DumbsmartRepositoriesBundle\Repository\RepositoryFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

class DoctrineConfigurer
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
     * @var ObjectIdentifierFactory
     */
    private $oif;

    /**
     * @var DoctrineMetadataFactory
     */
    private $dmf;

    /**
     * @var RepositoryFactory
     */
    private $rf;

    /**
     * @param MetadataManager         $mm
     * @param RepositoryManager       $rm
     * @param ObjectIdentifierFactory $oif
     * @param DoctrineMetadataFactory $dmf
     * @param RepositoryFactory       $rf
     */
    public function __construct(
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        DoctrineMetadataFactory $dmf,
        RepositoryFactory $rf
    ) {
        $this->mm = $mm;
        $this->rm = $rm;
        $this->oif = $oif;
        $this->dmf = $dmf;
        $this->rf = $rf;
    }

    /**
     * @param ClassMetadataFactory $factory
     */
    public function configure(ClassMetadataFactory $factory)
    {
        foreach ($factory->getAllMetadata() as $metadata) {
            $className = $metadata->getName();
            $parentClassName = $this->getParentClassName($metadata);
            $identifier = new AliasedObjectIdentifier(
                $className,
                $this->oif->createDoctrineObjectIdentifier($metadata)
            );

            try {
                $this->mm->getMetadataForClassName($className);
            } catch (MetadataNotFoundException $e) {
                $this->mm->addMetadata($className, $this->dmf->createMetadata($metadata, $identifier));
            }

            if ($className === $parentClassName) {
                try {
                    $repository = $this->rm->getRepositoryForClassName($className);
                } catch (RepositoryNotFoundException $e) {
                    $repository = $this->rf->createRepository($metadata->getName(), $identifier);
                }
            } else {
                try {
                    $repository = $this->rm->getRepositoryForClassName($parentClassName);
                } catch (RepositoryNotFoundException $e) {
                    $repository = $this->rf->createRepository(
                        $factory->getMetadataFor($parentClassName)->getName(),
                        $identifier
                    );
                }
                $this->rm->addRepository($parentClassName, $repository);
            }

            $this->rm->addRepository($className, $repository);
        }
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return string
     */
    private function getParentClassName(ClassMetadata $metadata)
    {
        // So far only single inheritance is supported
        if (count($metadata->parentClasses) > 0) {
            return $metadata->parentClasses[0];
        }

        return $metadata->getName();
    }
}
