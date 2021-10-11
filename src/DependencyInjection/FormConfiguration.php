<?php

namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class FormConfiguration implements ConfigurationInterface {

    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('form');

        $treeBuilder->getRootNode()
            ->useAttributeAsKey('alias')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('name')
                        ->isRequired()
                    ->end()
                    ->arrayNode('introduction')
                        ->beforeNormalization()->castToArray()->end()
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('success')
                        ->beforeNormalization()->castToArray()->end()
                        ->scalarPrototype()->end()
                    ->end()
                    ->scalarNode('role')
                        ->isRequired()
                    ->end()
                    ->variableNode('security')->end()
                    ->scalarNode('form_class')->isRequired()->end()
                    ->variableNode('items')->isRequired()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}