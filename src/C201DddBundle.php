<?php

namespace C201\Ddd;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Marko Vujnovic <mv@201created.de>
 * @since  2020-04-22
 */
class C201DddBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $namespaces = ['C201\Ddd\Events\Infrastructure\Store\Doctrine'];
        $directories = [realpath(__DIR__ . '/Events/Infrastructure/Store/Doctrine')];
        $managerParameters = [];
        $enabledParameter = false;
        $aliasMap = [];
        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                $namespaces,
                $directories,
                $managerParameters,
                $enabledParameter,
                $aliasMap
            )
        );
    }
}
