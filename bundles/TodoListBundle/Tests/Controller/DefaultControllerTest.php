<?php

namespace TodoListBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {

        $expected = "geeks";
        $actual = "Geeks";

        // Assert function to test whether expected
        // value is equal to actual or not
        $this->assertEquals(
            $expected,
            $actual,
            "actual value is not equals to expected"
        );
    }

    public function testEgal()
    {

        $expected = "geeks";
        $actual = "Geeks";

        // Assert function to test whether expected
        // value is equal to actual or not
        $this->assertEquals(
            1,
            1,
            "ce sont les mememes"
        );
    }
}
