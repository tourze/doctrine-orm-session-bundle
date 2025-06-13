<?php

namespace Tourze\DoctrineORMSessionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DoctrineORMSessionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // 设置默认参数
        $container->setParameter('tourze_doctrine_orm_session.lifetime', 1440);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
    }
}
