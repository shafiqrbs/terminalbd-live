<?php

/*
 * This file is part of the Docudex project.
 *
 * (c) Devnet Limited <http://www.devnetlimited.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\Manager;

use Core\UserBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager as BaseManager;

class UserManager extends BaseManager
{
    public function getUserQuery()
    {
        $qb = $this->repository->createQueryBuilder('u');
        $query = $qb
            ->getQuery();
        return $query;
    }

    public function changeUserPassword(User $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $this->updatePassword($user);

        if (0 !== strlen($newPassword)) {
            $this->objectManager->persist($user);
            $this->objectManager->flush();
        }
    }
}