<?php

/*
 * This file is part of the Docudex project.
 *
 * (c) Mohammad Emran Hasan <phpfour@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\Permission;

use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;

/**
 * Role Hierarchy
 *
 * @author Mohammad Emran Hasan <phpfour@gmail.com>
 */
class RoleHierarchy extends BaseRoleHierarchy
{
    public function __construct(array $hierarchy, PermissionBuilder $permissionBuilder)
    {
        parent::__construct($permissionBuilder->getPermissionHierarchy());
    }
} 