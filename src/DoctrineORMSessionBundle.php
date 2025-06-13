<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\DoctrineORMSessionBundle\DependencyInjection\Compiler\SessionHandlerCompilerPass;

class DoctrineORMSessionBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        
        $container->addCompilerPass(new SessionHandlerCompilerPass());
    }
}
