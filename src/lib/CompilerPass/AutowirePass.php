<?php

/*
 * This file is part of the xphere\autowire project
 *
 * (c) Berny Cantos <be@rny.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xphere\Autowire\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutowirePass implements CompilerPassInterface
{
    private $serviceId;
    private $tagName;
    private $autowire;

    public function __construct($serviceId, $tagName, $autowire = [])
    {
        $this->serviceId = $serviceId;
        $this->tagName = $tagName;
        $this->autowire = $autowire;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->serviceId)) {
            return;
        }

        $autowire = array_merge(
            $this->findAutowireServices($container),
            $this->autowire
        );

        if (empty($autowire)) {
            $container->removeDefinition($this->serviceId);
        } else {
            $definition = $container->findDefinition($this->serviceId);
            $definition->addArgument($autowire);
        }
    }

    private function findAutowireServices(ContainerBuilder $container)
    {
        $autowire = [];
        $taggedServices = $container->findTaggedServiceIds($this->tagName);
        foreach ($taggedServices as $serviceId => $tags) {
            $definition = $container->getDefinition($serviceId);
            $class = $definition->getClass();
            $autowire[$class] = $serviceId;
        }

        return $autowire;
    }
}
