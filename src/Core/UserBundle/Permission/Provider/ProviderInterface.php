<?php

/*
 * This file is part of the Docudex project.
 *
 * (c) Mohammad Emran Hasan <phpfour@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\Permission\Provider;

/**
 * Provides the permission to be exposed by a bundle.
 *
 * @author Mohammad Emran Hasan <phpfour@gmail.com>
 */
interface ProviderInterface
{
    public function getPermissions();
} 