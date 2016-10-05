<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Repository;

use carlosV2\DumbsmartRepositories\Persister;
use PhpSpec\ObjectBehavior;

class FrontRepositoryFactorySpec extends ObjectBehavior
{
    function it_creates_a_FrontRepository_for_a_class(Persister $persister)
    {
        $persister->clear('my_class')->shouldBeCalled();

        $this->beConstructedWith($persister);
        $repository = $this->getRepository('my_class');
        $repository->shouldBeAnInstanceOf('carlosV2\DumbsmartRepositories\FrontRepository');
        $repository->clear();
    }
}
