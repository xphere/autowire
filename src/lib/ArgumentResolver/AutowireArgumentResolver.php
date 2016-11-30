<?php

/*
 * This file is part of the xphere\autowire project
 *
 * (c) Berny Cantos <be@rny.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xphere\Autowire\ArgumentResolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class AutowireArgumentResolver implements ArgumentValueResolverInterface
{
    private $container;
    private $mapping;

    public function __construct(ContainerInterface $container, array $mapping)
    {
        $this->container = $container;
        $this->mapping = $mapping;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return isset($this->mapping[$argument->getType()]);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $type = $argument->getType();
        $serviceId = $this->mapping[$type];

        yield $this->container->get($serviceId);
    }
}
