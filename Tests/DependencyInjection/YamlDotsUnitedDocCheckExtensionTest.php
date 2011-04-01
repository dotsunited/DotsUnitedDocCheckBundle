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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class YamlDotsUnitedDocCheckExtensionTest extends DotsUnitedDocCheckExtensionTest
{
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/yml'));
        $loader->load($file.'.yml');
    }
}
