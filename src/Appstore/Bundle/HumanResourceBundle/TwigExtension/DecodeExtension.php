<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 5/26/19
 * Time: 4:05 PM
 */

namespace Appstore\Bundle\HumanResourceBundle\TwigExtension;


use Symfony\Component\DependencyInjection\ContainerInterface;
use \Twig_Extension;

class DecodeExtension extends Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'some.extension';
    }

    public function getFilters() {
        return array(
            'json_decode'   => new \Twig_Filter_Method($this, 'jsonDecode'),
        );
    }

    public function jsonDecode($str) {
        return json_decode($str);
    }
}