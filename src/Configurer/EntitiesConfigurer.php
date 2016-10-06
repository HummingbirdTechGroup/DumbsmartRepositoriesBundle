<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException;
use carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\Metadata\EntityMetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier;
use carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\ObjectIdentifierFactory;
use carlosV2\DumbsmartRepositoriesBundle\Repository\RepositoryFactory;

class EntitiesConfigurer
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
     * @var EntityMetadataFactory
     */
    private $emf;

    /**
     * @var RepositoryFactory
     */
    private $rf;

    /**
     * @param MetadataManager         $mm
     * @param RepositoryManager       $rm
     * @param ObjectIdentifierFactory $oif
     * @param EntityMetadataFactory   $emf
     * @param RepositoryFactory       $rf
     */
    public function __construct(
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        EntityMetadataFactory $emf,
        RepositoryFactory $rf
    ) {
        $this->mm = $mm;
        $this->rm = $rm;
        $this->oif = $oif;
        $this->emf = $emf;
        $this->rf = $rf;
    }

    /**
     * @param array $entities
     */
    public function configure(array $entities)
    {
        foreach ($this->getComposedEntities($entities) as $className => $entity) {
            $identifier = new AliasedObjectIdentifier(
                $className,
                $this->oif->createPropertyObjectIdentifier($entity['id'])
            );

            try {
                $this->mm->getMetadataForClassName($className);
            } catch (MetadataNotFoundException $e) {
                $this->mm->addMetadata($className, $this->emf->createMetadata($entity['relations'], $identifier));
            }

            if ($className === $entity['extends']) {
                try {
                    $repository = $this->rm->getRepositoryForClassName($className);
                } catch (RepositoryNotFoundException $e) {
                    $repository = $this->rf->createRepository($className, $identifier);
                }
            } else {
                try {
                    $repository = $this->rm->getRepositoryForClassName($entity['extends']);
                } catch (RepositoryNotFoundException $e) {
                    $repository = $this->rf->createRepository($entity['extends'], $identifier);
                }
                $this->rm->addRepository($entity['extends'], $repository);
            }

            $this->rm->addRepository($className, $repository);
        }
    }

    /**
     * @param array $entities
     *
     * @return \Generator
     *
     * @throw \RuntimeException
     */
    private function getComposedEntities(array $entities)
    {
        foreach ($entities as $className => $entity) {
            if (array_key_exists('extends', $entity)) {
                $this->assertParentClassNameExists($entity['extends'], $entities);
                $this->assertParentClassHasIdField($entity['extends'], $entities);

                yield $className => [
                    'id' => $entities[$entity['extends']]['id'],
                    'extends' => $entity['extends'],
                    'relations' => array_merge(
                        $entities[$entity['extends']]['relations'],
                        $entity['relations']
                    )
                ];
            } else {
                yield $className => [
                    'id' => $entity['id'],
                    'extends' => $className,
                    'relations' => $entity['relations']
                ];
            }
        }
    }

    /**
     * @param string $className
     * @param array  $entities
     *
     * @throw \RuntimeException
     */
    private function assertParentClassNameExists($className, array $entities)
    {
        if (!array_key_exists($className, $entities)) {
            throw new \RuntimeException(sprintf('Parent class `%s` not found in the given list of entities.', $className));
        }
    }

    /**
     * @param string $className
     * @param array  $entities
     *
     * @throw \RuntimeException
     */
    private function assertParentClassHasIdField($className, array $entities)
    {
        if (!array_key_exists('id', $entities[$className])) {
            throw new \RuntimeException(sprintf('Parent class `%s` has not a defined id in the given list of entities.', $className));
        }
    }
}
