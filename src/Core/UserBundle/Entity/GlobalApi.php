<?php

/*
 * This file is part of the Docudex project.
 *
 * (c) Devnet Limited <http://www.devnetlimited.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**

 * @ORM\Table(name="global_api")
 * @ORM\Entity(repositoryClass="Core\UserBundle\Repository\GlobalApiRepository")
 * @ORM\HasLifecycleCallbacks
 */
class GlobalApi
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}