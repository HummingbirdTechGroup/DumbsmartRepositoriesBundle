<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\FrontRepository;
use carlosV2\DumbsmartRepositories\Persister;

class FrontRepositoryFactory
{
    /**
     * @var Persister
     */
    private $persister;

    /**
     * @param Persister $persister
     */
    public function __construct(Persister $persister)
    {
        $this->persister = $persister;
    }

    /**
     * @param string $className
     *
     * @return FrontRepository
     */
    public function getRepository($className)
    {
        return new FrontRepository($this->persister, $className);
    }
}
