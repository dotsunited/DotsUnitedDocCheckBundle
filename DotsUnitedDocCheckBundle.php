<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use DotsUnited\DocCheckBundle\DependencyInjection\Security\Factory\DocCheckFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Bundle.
 */
class DotsUnitedDocCheckBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new DocCheckFactory());
    }
}
