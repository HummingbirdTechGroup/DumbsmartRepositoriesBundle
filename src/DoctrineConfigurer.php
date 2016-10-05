<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException;
use carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\AliasedMetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\MetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\ObjectIdentifierFactory;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\RepositoryFactory;
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
     * @var MetadataFactory
     */
    private $mf;

    /**
     * @var RepositoryFactory
     */
    private $rf;

    /**
     * @var AliasedMetadataFactory
     */
    private $amf;

    /**
     * @param MetadataManager         $mm
     * @param RepositoryManager       $rm
     * @param ObjectIdentifierFactory $oif
     * @param MetadataFactory         $mf
     * @param RepositoryFactory       $rf
     * @param AliasedMetadataFactory  $amf
     */
    public function __construct(
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        MetadataFactory $mf,
        RepositoryFactory $rf,
        AliasedMetadataFactory $amf
    ) {
        $this->mm = $mm;
        $this->rm = $rm;
        $this->oif = $oif;
        $this->mf = $mf;
        $this->rf = $rf;
        $this->amf = $amf;
    }

    /**
     * @param ClassMetadataFactory $factory
     */
    public function configureEntities(ClassMetadataFactory $factory)
    {
        foreach ($factory->getAllMetadata() as $metadata) {
            $className = $metadata->getName();
            $parentClassName = $this->getParentClassName($metadata);
            $identifier = new AliasedObjectIdentifier(
                $className,
                $this->oif->createObjectIdentifier($metadata)
            );

            try {
                $this->mm->getMetadataForClassName($className);
            } catch (MetadataNotFoundException $e) {
                $this->mm->addMetadata($className, $this->mf->createMetadata($metadata, $identifier));
            }

            if ($className === $parentClassName) {
                try {
                    $repository = $this->rm->getRepositoryForClassName($className);
                } catch (RepositoryNotFoundException $e) {
                    $repository = $this->rf->createRepository($metadata, $identifier);
                }
            } else {
                try {
                    $repository = $this->rm->getRepositoryForClassName($parentClassName);
                } catch (RepositoryNotFoundException $e) {
                    $repository = $this->rf->createRepository($factory->getMetadataFor($parentClassName), $identifier);
                }
                $this->rm->addRepository($parentClassName, $repository);
            }

            $this->rm->addRepository($className, $repository);
        }
    }

    /**
     * @param array $aliases
     *
     * @throws MetadataNotFoundException
     * @throws RepositoryNotFoundException
     */
    public function configureAliases(array $aliases)
    {
        foreach ($aliases as $alias => $config) {
            $this->mm->getMetadataForClassName($config['class'])->getObjectIdentifier()->setAlias($alias);

            $this->mm->addMetadata($alias, $this->amf->createAliasedMetadata($config));
            $this->rm->addRepository($alias, $this->rm->getRepositoryForClassName($config['class']));
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
