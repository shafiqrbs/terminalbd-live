<?php
namespace Appstore\Bundle\DomainUserBundle\Twig;


class CulculationExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('gpPercentage', array($this, 'gpPercentage')),
        );
    }

    public function gpPercentage($number, $percentage = 8)
    {
        return round(($number * $percentage)/100);
    }

    public function productVat($number, $percentage = 15)
    {
        return round(($number * $percentage)/100);
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }
}