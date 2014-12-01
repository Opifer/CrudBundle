<?php

namespace Opifer\CrudBundle\Tests\Transformer;

use Opifer\CrudBundle\Tests\TestData\User;
use Opifer\CrudBundle\Transformer\SlugTransformer;

class SlugTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $routes = [
            'users' => 'Opifer\CrudBundle\Tests\TestData\User'
        ];

        $transformer = new SlugTransformer($routes);
        $actual = $transformer->transform('users');

        $this->assertEquals(new User, $actual);
    }

    /**
     * @expectedException Opifer\CrudBundle\Exception\IncorrectRouteConfigException
     */
    public function testTransformWithIncorrectConfiguration()
    {
        $routes = [
            'users' => 'User'
        ];

        $transformer = new SlugTransformer($routes);
        $actual = $transformer->transform('users');
    }
}
