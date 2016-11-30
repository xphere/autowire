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

class CompilerPass implements CompilerPassInterface
{
    private $passes;

    public function __construct(CompilerPassInterface ...$passes)
    {
        $this->passes = $passes;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($this->passes as $pass) {
            $pass->process($container);
        }
    }

    public function addCompilerPass(CompilerPassInterface $pass)
    {
        $this->passes[] = $pass;
    }
}
