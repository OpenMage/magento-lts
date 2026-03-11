<?php

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Mage_Core_Model_SymfonyKernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        /** @var array<class-string, array<string, bool>> $bundles */
        $bundles = [
            Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
        ];
        foreach ($bundles as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $codeDir = Mage::getBaseDir('code') . DS . 'local';
        $container->addCompilerPass(new Mage_Core_Model_SymfonyKernelCompilerPass($codeDir));
    }

    /** @phpstan-ignore method.unused (called by MicroKernelTrait) */
    private function configureContainer(
        ContainerConfigurator $container
    ): void {
        $configFolder = Mage::getBaseDir('etc');
        $container->import($configFolder . DS . 'openmagedi.yaml');
    }
}
