<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class DotsUnitedDocCheckExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('doccheck.xml');
        $loader->load('security.xml');

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->process($configuration->getConfigTree(), $configs);

        if (isset($config['login_id'])) {
            $container->setParameter('dotsunited_doccheck.login_id', $config['login_id']);
        }

        if (isset($config['login_form'])) {
            $this->registerLoginFormConfiguration($config['login_form'], $container);
        }
    }

    /**
     * Loads the login form configuration.
     *
     * @param array $config A login form configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    private function registerLoginFormConfiguration(array $config, ContainerBuilder $container)
    {
        foreach (array('template', 'base_url', 'append_session_id', 'special_parameters', 'csrf_protection', 'csrf_field_name', 'secret') as $key) {
            if (isset($config[$key])) {
                $container->setParameter('dotsunited_doccheck.login_form.' . $key, $config[$key]);
            }
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }
}
