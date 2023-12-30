<?php

/*
 * This file is part of the Docudex project.
 *
 * (c) Mohammad Emran Hasan <phpfour@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Compiles the permission hierarchy.
 *
 * @author Mohammad Emran Hasan <phpfour@gmail.com>
 */
class PermissionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('docudex.user.permission_builder')) {
            return;
        }

        $definition = $container->getDefinition('docudex.user.permission_builder');
        $taggedServices = $container->findTaggedServiceIds('docudex.user.permission_provider');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addProvider',
                    array(new Reference($id), $attributes["alias"])
                );
            }
        }
    }
}