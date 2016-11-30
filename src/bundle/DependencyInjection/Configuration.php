<?php

/*
 * This file is part of the xphere\autowire project
 *
 * (c) Berny Cantos <be@rny.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xphere\Bundle\AutowireBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder();
        $root = $tree->root($this->alias);

        $root
            ->addDefaultsIfNotSet()
            ->append($this->setupController())
        ;

        return $tree;
    }

    private function setupController()
    {
        $tree = new TreeBuilder();
        $root = $tree->root('controller');

        $strategies = [
            'make_public',
            'only_public',
        ];

        $root
            ->append($this->setupClassMapping())
            ->children()
                ->scalarNode('service_id')
                    ->defaultValue('xphere.autowire.argument_resolver')
                ->end()
                ->scalarNode('tag_name')
                    ->defaultValue('xphere_autowire.controller')
                ->end()
                ->scalarNode('strategy')
                    ->defaultValue('only_public')
                    ->validate()
                        ->ifNotInArray($strategies)
                        ->then(function ($v) use ($strategies) {
                            throw new \InvalidArgumentException(sprintf(
                                'Strategy must be one of ["%s"], found "%s"',
                                implode('", "', $strategies),
                                $v
                            ));
                        })
                    ->end()
                ->end()
            ->end()
        ;

        return $root;
    }

    private function setupClassMapping($name = 'mapping')
    {
        $tree = new TreeBuilder();
        $root = $tree->root($name);

        $root
            ->prototype('scalar')
            ->end()
        ;

        return $root;
    }
}
