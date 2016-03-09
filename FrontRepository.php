<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\Persister;
use Everzet\PersistedObjects\Repository;

class FrontRepository implements Repository
{
    /**
     * @var Persister
     */
    private $persister;

    /**
     * @var string
     */
    private $className;

    /**
     * @param Persister $persister
     * @param string    $className
     */
    public function __construct(Persister $persister, $className)
    {
        $this->persister = $persister;
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function save($object)
    {
        $this->persister->save($object);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object)
    {
        $this->persister->remove($object);
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        return $this->persister->findById($this->className, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->persister->getAll($this->className);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->persister->clear($this->className);
    }
}
