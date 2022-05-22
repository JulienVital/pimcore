<?php
namespace Tests;

use Pimcore\Test\WebTestCase;

use Nelmio\Alice\Loader\NativeLoader;

class Foo extends WebTestCase
{

    public function testFoo()
    {
        $this->assertTrue(true);
    }

    public function testLoadFixture()
    {
        $loader = new NativeLoader();
        $objectSet = $loader->loadFile(__DIR__.'/related_dummy.yaml');
        dd($objectSet);
    }
}

