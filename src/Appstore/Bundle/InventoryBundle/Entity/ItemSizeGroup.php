<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ItemTypeGrouping
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\ItemTypeGroupingRepository")
 */
class ItemSizeGroup
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
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", inversedBy="sizeGroup" , cascade={"detach","merge"})
     * @ORM\OrderBy({"id" = "DESC"})
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    protected $sizes;


    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="sizeGroup")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $inventoryConfig;


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
     * @return $inventoryConfig
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param InventoryConfig $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }

    /**
     * @return $sizes
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @param InventoryConfig $sizes
     */
    public function setSizes($sizes)
    {
        $this->sizes = $sizes;
    }


}

