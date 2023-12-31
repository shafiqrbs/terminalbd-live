<?php

namespace Setting\Bundle\AppearanceBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Connection;


use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RequestManager
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

class MenuManager

{

    /**
     * @var EntityManager
     */
    protected  $em;
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    public function  __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();
    }

    function urlSlug($str, $options = array()) {

        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => false,
        );

        // Merge options
        $options = array_merge($defaults, $options);


        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);


        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;

    }


}
