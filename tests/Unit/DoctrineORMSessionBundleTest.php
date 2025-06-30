<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\DoctrineORMSessionBundle\DependencyInjection\Compiler\SessionHandlerCompilerPass;
use Tourze\DoctrineORMSessionBundle\DoctrineORMSessionBundle;

class DoctrineORMSessionBundleTest extends TestCase
{
    private DoctrineORMSessionBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new DoctrineORMSessionBundle();
    }

    public function testBuildAddsSessionHandlerCompilerPass(): void
    {
        $container = new ContainerBuilder();
        
        $this->bundle->build($container);
        
        $passes = $container->getCompilerPassConfig()->getPasses();
        
        $hasSessionHandlerPass = false;
        foreach ($passes as $pass) {
            if ($pass instanceof SessionHandlerCompilerPass) {
                $hasSessionHandlerPass = true;
                break;
            }
        }
        
        $this->assertTrue($hasSessionHandlerPass, 'SessionHandlerCompilerPass should be added to the container');
    }
}