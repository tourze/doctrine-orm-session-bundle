<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SessionHandlerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // 如果用户没有显式配置 session handler，自动设置为 doctrine_orm
        if (!$container->hasParameter('session.handler.service_id')) {
            $container->setParameter('session.handler.service_id', 'doctrine_orm_session.handler');
        }

        // 如果用户配置了 handler_id 为 'doctrine_orm'，则替换为我们的服务
        if ($container->hasParameter('session.handler_id') &&
            $container->getParameter('session.handler_id') === 'doctrine_orm') {
            $container->setAlias('session.handler', 'doctrine_orm_session.handler');
        }
    }
}
