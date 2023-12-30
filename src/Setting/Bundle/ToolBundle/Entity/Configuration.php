<?php

namespace Setting\Bundle\ToolBundle\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Icon
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="")
 */
class Configuration
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
     * @var string
     *
     * @ORM\Column(name="googleMapApiKey", type="string", length=255, nullable = true)
     */
    private $googleMapApiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="recaptchaKey", type="string", length=255, nullable = true)
     */
    private $recaptchaKey;



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
     * @return string
     */
    public function getGoogleMapApiKey()
    {
        return $this->googleMapApiKey;
    }

    /**
     * @param string $googleMapApiKey
     */
    public function setGoogleMapApiKey($googleMapApiKey)
    {
        $this->googleMapApiKey = $googleMapApiKey;
    }

    /**
     * @return string
     */
    public function getRecaptchaKey()
    {
        return $this->recaptchaKey;
    }

    /**
     * @param string $recaptchaKey
     */
    public function setRecaptchaKey($recaptchaKey)
    {
        $this->recaptchaKey = $recaptchaKey;
    }


}

