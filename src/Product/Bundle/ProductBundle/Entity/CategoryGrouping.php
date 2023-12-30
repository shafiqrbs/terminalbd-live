<?php

namespace Product\Bundle\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryGrouping
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Product\Bundle\ProductBundle\Entity\CategoryGroupingRepository")
 */
class CategoryGrouping
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="categoryGrouping")
     **/

    protected $user;


    /**
     * @ORM\ManyToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="categoryGrouping" , cascade={"detach","merge"})
     **/

    protected $categories;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }
}
