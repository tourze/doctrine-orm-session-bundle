<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\DoctrineORMSessionBundle\DependencyInjection\Compiler\SessionHandlerCompilerPass;

class SessionHandlerCompilerPassTest extends TestCase
{
    private SessionHandlerCompilerPass $compilerPass;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->compilerPass = new SessionHandlerCompilerPass();
        $this->container = new ContainerBuilder();
    }

    public function testProcessSetsDefaultHandlerWhenNotConfigured(): void
    {
        $this->compilerPass->process($this->container);

        $this->assertTrue($this->container->hasParameter('session.handler.service_id'));
        $this->assertSame('doctrine_orm_session.handler', $this->container->getParameter('session.handler.service_id'));
    }

    public function testProcessDoesNotOverrideExistingHandler(): void
    {
        $this->container->setParameter('session.handler.service_id', 'existing.handler');

        $this->compilerPass->process($this->container);

        $this->assertSame('existing.handler', $this->container->getParameter('session.handler.service_id'));
    }

    public function testProcessSetsAliasWhenHandlerIdIsDoctrineOrm(): void
    {
        $this->container->setParameter('session.handler_id', 'doctrine_orm');

        $this->compilerPass->process($this->container);

        $this->assertTrue($this->container->hasAlias('session.handler'));
        $this->assertSame('doctrine_orm_session.handler', (string) $this->container->getAlias('session.handler'));
    }

    public function testProcessDoesNotSetAliasWhenHandlerIdIsDifferent(): void
    {
        $this->container->setParameter('session.handler_id', 'native');

        $this->compilerPass->process($this->container);

        $this->assertFalse($this->container->hasAlias('session.handler'));
    }
}