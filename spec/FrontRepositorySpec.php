<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\Persister;
use Everzet\PersistedObjects\Repository;
use PhpSpec\ObjectBehavior;

class FrontRepositorySpec extends ObjectBehavior
{
    function let(Persister $persister)
    {
        $this->beConstructedWith($persister, 'my_class');
    }

    function it_is_a_Repository()
    {
        $this->shouldHaveType(Repository::class);
    }

    function it_saves_an_object(Persister $persister)
    {
        $object = new \stdClass();

        $persister->save($object)->shouldBeCalled();

        $this->save($object);
    }

    function it_removes_an_object(Persister $persister)
    {
        $object = new \stdClass();

        $persister->remove($object)->shouldBeCalled();

        $this->remove($object);
    }

    function it_finds_an_object_by_its_id(Persister $persister)
    {
        $object = new \stdClass();

        $persister->findById('my_class', 'id')->willReturn($object);

        $this->findById('id')->shouldReturn($object);
    }


    function it_gets_all_objects(Persister $persister)
    {
        $object = new \stdClass();

        $persister->getAll('my_class')->willReturn([$object]);

        $this->getAll()->shouldReturn([$object]);
    }

    function it_clears_the_repository(Persister $persister)
    {
        $persister->clear('my_class')->shouldBeCalled();

        $this->clear();
    }
}
