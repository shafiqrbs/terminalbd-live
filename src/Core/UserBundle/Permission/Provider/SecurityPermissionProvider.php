<?php

namespace Core\UserBundle\Permission\Provider;

/**
 * Security Provider
 *
 * @author Mohammad Emran Hasan <phpfour@gmail.com>
 */
class SecurityPermissionProvider implements ProviderInterface
{
    public function getPermissions()
    {
        return array(
            'ROLE_VENDOR' => array(),
            'ROLE_SYNDICATE' => array(),
            'ROLE_ADMIN' => array(),
            'ROLE_MANAGE_USERS'      => array('ROLE_MANAGE_USERS_VIEW', 'ROLE_MANAGE_ADD', 'ROLE_MANAGE_UPDATE', 'ROLE_MANAGE_DELETE', 'ROLE_MANAGE_SUSPEND'),
            'ROLE_MANAGE_GROUPS'     => array('ROLE_MANAGE_GROUPS_VIEW', 'ROLE_GROUP_ADD', 'ROLE_GROUP_UPDATE', 'ROLE_GROUP_DELETE'),
            'ROLE_MANAGE_PERMISSION' => array('ROLE_MANAGE_USERS', 'ROLE_MANAGE_GROUPS'),
            'ROLE_MANAGE_SECURITY'   => array(
                'ROLE_MANAGE_USERS',
                'ROLE_MANAGE_GROUPS',
                'ROLE_MANAGE_PERMISSION',
                'ROLE_SYNDICATE',
                'ROLE_VENDOR'
            )
        );
    }
}