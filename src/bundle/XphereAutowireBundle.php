<?php

/*
 * This file is part of the xphere\autowire project
 *
 * (c) Berny Cantos <be@rny.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xphere\Bundle\AutowireBundle;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Xphere\Bundle\AutowireBundle\DependencyInjection\CompilerPass;
use Xphere\Bundle\AutowireBundle\DependencyInjection\AutowireExtension;

class XphereAutowireBundle implements BundleInterface
{
    use ContainerAwareTrait;

    private $compilerPass;
    private $extension;

    public function getName()
    {
        return 'XphereAutowireBundle';
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            $this->compilerPass()
        );
    }

    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new AutowireExtension(
                $this->compilerPass()
            );
        }

        return $this->extension;
    }

    public function boot()
    {
    }

    public function shutdown()
    {
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getPath()
    {
        return __DIR__;
    }
    public function getParent()
    {
    }

    private function compilerPass()
    {
        if (!$this->compilerPass) {
            $this->compilerPass = new CompilerPass();
        }

        return $this->compilerPass;
    }
}
