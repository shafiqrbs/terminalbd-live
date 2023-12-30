<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Product\Bundle\ProductBundle\Entity\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * FeatureWidgetItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Repository\FeatureWidgetItemRepository")
 */
class FeatureWidgetItem
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidget", inversedBy="featureWidgetItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $featureWidget;

     /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Feature", inversedBy="featureWidgetItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"ordering" = "ASC"})
     **/
    private  $feature;

    /**
     * @var integer
     *
     * @ORM\Column(name="divWidth", type="integer",  nullable=true)
     */
    private $divWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="divHeight", type="integer", nullable=true)
     */
     private $divHeight;

    /**
     * @var smallint
     *
     * @ORM\Column(name="sorting", type="smallint" , nullable=true)
     */
    private $sorting;

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
     * @return smallint
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param smallint $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }


   /**
     * @return int
     */
    public function getDivWidth()
    {
        return $this->divWidth;
    }

    /**
     * @param int $divWidth
     */
    public function setDivWidth($divWidth)
    {
        $this->divWidth = $divWidth;
    }

    /**
     * @return int
     */
    public function getDivHeight()
    {
        return $this->divHeight;
    }

    /**
     * @param int $divHeight
     */
    public function setDivHeight($divHeight)
    {
        $this->divHeight = $divHeight;
    }

    /**
     * @return FeatureWidget
     */
    public function getFeatureWidget()
    {
        return $this->featureWidget;
    }

    /**
     * @param FeatureWidget $featureWidget
     */
    public function setFeatureWidget($featureWidget)
    {
        $this->featureWidget = $featureWidget;
    }

    /**
     * @return Feature
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param Feature $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }
}
