<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Tests\Twig\Extension;

use DotsUnited\DocCheckBundle\Twig\Extension\DocCheckExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DocCheckExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderLoginForm()
    {
        $expected = new \stdClass;

        $helper = $this->getMockBuilder('DotsUnited\DocCheckBundle\Templating\Helper\DocCheckHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $helper
            ->expects($this->once())
            ->method('loginForm')
            ->with(array(), 'DotsUnitedDocCheck::loginForm.html.twig')
            ->will($this->returnValue($expected));

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->getMock();

        $container
            ->expects($this->once())
            ->method('get')
            ->with('dotsunited_doccheck.templating.helper')
            ->will($this->returnValue($helper));

        $extension = new DocCheckExtension($container);

        $this->assertSame($expected, $extension->renderLoginForm());
    }

    public function testGetFunctions()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->getMock();

        $extension = new DocCheckExtension($container);

        $this->assertArrayHasKey('doccheck_login_form', $extension->getFunctions());
    }

    public function testGetName()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->getMock();

        $extension = new DocCheckExtension($container);

        $this->assertEquals('doccheck', $extension->getName());
    }
}
