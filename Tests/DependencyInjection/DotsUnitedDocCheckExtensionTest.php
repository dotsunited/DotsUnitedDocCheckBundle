<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\Tests\DependencyInjection;

use DotsUnited\DocCheckBundle\Tests\TestCase;
use DotsUnited\DocCheckBundle\DependencyInjection\DotsUnitedDocCheckExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

abstract class DotsUnitedDocCheckExtensionTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function loadFromFile(ContainerBuilder $container, $file);

    public function testGlobals()
    {
        $container = $this->createContainerFromFile('full');

        $this->assertEquals(123456789, $container->getParameter('dotsunited_doccheck.login_id'));
    }

    public function testLoginForm()
    {
        $container = $this->createContainerFromFile('full');

        $this->assertEquals('xl_red', $container->getParameter('dotsunited_doccheck.login_form.template'));
        $this->assertEquals('http://example.com', $container->getParameter('dotsunited_doccheck.login_form.base_url'));
        $this->assertTrue($container->getParameter('dotsunited_doccheck.login_form.append_session_id'));
        $this->assertEquals('special1=foo', $container->getParameter('dotsunited_doccheck.login_form.special_parameters'));
        $this->assertFalse($container->getParameter('dotsunited_doccheck.login_form.csrf_protection'));
        $this->assertEquals('_doccheck_token', $container->getParameter('dotsunited_doccheck.login_form.csrf_field_name'));
        $this->assertEquals('$3cr3t', $container->getParameter('dotsunited_doccheck.login_form.secret'));
    }

    protected function createContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.bundles'          => array('DotsUnitedDocCheckBundle' => 'DotsUnited\\DocCheckBundle\\DotsUnitedDocCheckBundle'),
            'kernel.cache_dir'        => __DIR__,
            'kernel.compiled_classes' => array(),
            'kernel.debug'            => false,
            'kernel.environment'      => 'test',
            'kernel.name'             => 'kernel',
            'kernel.root_dir'         => __DIR__,
        )));

        // Set default parameters normally provided by FrameworkBundle
        $container->setParameter('form.csrf_protection.enabled', true);
        $container->setParameter('form.csrf_protection.field_name', '_token');
        $container->setParameter('form.csrf_protection.secret', 'secret');
        $container->setParameter('form.csrf_provider.class', 'Symfony\\Component\\Form\\CsrfProvider\\SessionCsrfProvider');
        $container->setParameter('form.context.class', 'Symfony\\Component\\Form\\FormContext');

        return $container;
    }

    protected function createContainerFromFile($file)
    {
        $container = $this->createContainer();
        $container->registerExtension(new DotsUnitedDocCheckExtension());
        $this->loadFromFile($container, $file);

        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
