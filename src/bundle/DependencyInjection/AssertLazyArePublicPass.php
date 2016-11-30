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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AssertLazyArePublicPass implements CompilerPassInterface
{
    private $serviceIds;

    public function __construct(array $serviceIds)
    {
        $this->serviceIds = $serviceIds;
    }

    public function process(ContainerBuilder $container)
    {
        $nonPublic = [];
        foreach ($this->serviceIds as $serviceId) {
            $definition = $container->findDefinition($serviceId);
            if (!$definition->isPublic()) {
                $nonPublic[] = $serviceId;
            }
        }

        if (empty($nonPublic)) {
            return;
        }

        throw new \Exception(sprintf(
            'Autowiring services must be public, found "%s" private',
            implode('", "', $nonPublic)
        ));
    }
}
