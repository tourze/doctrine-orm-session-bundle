<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\DoctrineORMSessionBundle\DependencyInjection\DoctrineORMSessionExtension;

class DoctrineORMSessionExtensionTest extends TestCase
{
    private DoctrineORMSessionExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new DoctrineORMSessionExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadSetsDefaultParameters(): void
    {
        $this->extension->load([], $this->container);

        $this->assertTrue($this->container->hasParameter('tourze_doctrine_orm_session.lifetime'));
        $this->assertSame(1440, $this->container->getParameter('tourze_doctrine_orm_session.lifetime'));
    }

    public function testLoadServicesDefinitions(): void
    {
        $this->extension->load([], $this->container);

        // 验证核心服务已加载
        $this->assertTrue($this->container->hasAlias('doctrine_orm_session.handler'));
        $this->assertTrue($this->container->hasDefinition('Tourze\DoctrineORMSessionBundle\Handler\DoctrineSessionHandler'));
    }
}