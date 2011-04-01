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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dotsunited_doccheck');

        $rootNode
            ->children()
                ->scalarNode('login_id')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;

        $this->addLoginFormSection($rootNode);

        return $treeBuilder->buildTree();
    }

    private function addLoginFormSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('login_form')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('template')->end()
                        ->scalarNode('base_url')->end()
                        ->booleanNode('append_session_id')->end()
                        ->scalarNode('special_parameters')->end()
                        ->booleanNode('csrf_protection')->end()
                        ->scalarNode('csrf_field_name')->end()
                        ->scalarNode('secret')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
