<?php

/*
 * This file is part of the DotsUnitedDocCheckBundle.
 *
 * (c) Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file Resources/meta/LICENSE.
 */

namespace DotsUnited\DocCheckBundle\DependencyInjection\Security\Factory;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class DocCheckFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('key', uniqid());
        $this->addOption('roles', array());
        $this->addOption('csrf_parameter', '%dotsunited_doccheck.login_form.csrf_field_name%');
        $this->addOption('csrf_page_id', '%dotsunited_doccheck.login_form.csrf_page_id%');
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'dotsunited-doccheck';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        $node
            ->children()
                ->scalarNode('csrf_provider')->cannotBeEmpty()->end()
                ->arrayNode('roles')->cannotBeEmpty()->prototype('scalar')->end()->end()
            ->end()
        ;
    }

    protected function getListenerId()
    {
        return 'dotsunited_doccheck.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'dotsunited_doccheck.security.authentication.provider.'.$id;

        $definition = $container
            ->setDefinition($providerId, new DefinitionDecorator('dotsunited_doccheck.security.authentication.provider'))
            ->setArgument(0, $config['key'])
            ->setArgument(1, $config['roles'])
        ;

        if (isset($config['provider'])) {
            $definition
                ->addArgument(new Reference($userProviderId))
                ->addArgument(new Reference('security.user_checker'))
            ;
        }

        return $providerId;
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = parent::createListener($container, $id, $config, $userProvider);

        if (isset($config['csrf_provider'])) {
            $container
                ->getDefinition($listenerId)
                ->addArgument(new Reference($config['csrf_provider']))
            ;
        }

        return $listenerId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPointId)
    {
        $entryPointId = 'dotsunited_doccheck.security.authentication.entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('dotsunited_doccheck.security.authentication.entry_point'))
            ->addArgument($config['login_path'])
            ->addArgument($config['use_forward'])
        ;

        return $entryPointId;
    }
}
