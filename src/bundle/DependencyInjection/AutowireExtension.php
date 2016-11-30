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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Xphere\Autowire\ArgumentResolver\AutowireArgumentResolver;
use Xphere\Autowire\CompilerPass\AutowirePass;

class AutowireExtension extends ConfigurableExtension
{
    private $pass;

    public function __construct(CompilerPass $pass)
    {
        $this->pass = $pass;
    }

    public function getAlias()
    {
        return 'xphere_autowire';
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->getAlias());
    }

    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $this->setupController($config['controller'], $container);
    }

    private function setupController(array $config, ContainerBuilder $container)
    {
        if (empty($config['mapping'])) {
            return;
        }

        $mapping = $config['mapping'];
        $serviceId = $config['service_id'];
        $strategy = $config['strategy'];
        $tagName = $config['tag_name'];

        $this->setupArgumentResolver($container, $serviceId);
        $this->setupStrategy($strategy, $mapping);
        $this->setupAutowire($serviceId, $tagName, $mapping);
    }

    private function setupArgumentResolver(ContainerBuilder $container, $serviceId)
    {
        $definition = new Definition(AutowireArgumentResolver::class, [
            new Reference('service_container'),
        ]);

        $definition
            ->addTag('controller.argument_value_resolver')
        ;

        $container->setDefinition($serviceId, $definition);
    }

    private function setupStrategy($strategy, $mapping)
    {
        switch ($strategy) {
            case 'make_public':
                $this->addCompilerPass(
                    new SetPublicPass($mapping)
                );
                break;

            case 'only_public':
                $this->addCompilerPass(
                    new AssertLazyArePublicPass($mapping)
                );
                break;
        }
    }

    private function setupAutowire($serviceId, $tagName, array $mapping)
    {
        $this->addCompilerPass(
            new AutowirePass($serviceId, $tagName, $mapping)
        );
    }

    private function addCompilerPass(CompilerPassInterface $pass)
    {
        $this->pass->addCompilerPass($pass);
    }
}
