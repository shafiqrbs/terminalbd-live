<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 10/9/15
 * Time: 8:05 AM
 */

namespace Setting\Bundle\ToolBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\FooterSetting;

class FooterSettingRepository extends EntityRepository {


    public function updateFooterSetting(FooterSetting $entity , $data ){

        $em = $this->_em;

        if(isset($data['copyRight']) and $data['copyRight'] != '') {
            $entity->setCopyRight($data['copyRight']);
        }

        $socialMedia = isset($data['socialMedia']) ? 1:0 ;
        $entity->setSocialMedia($socialMedia);

        $displayWebsite = isset($data['displayWebsite']) ? 1:0 ;
        $entity->setDisplayWebsite($displayWebsite);

        $turnOffBranding = isset($data['turnOffBranding']) ? 1:0 ;
        $entity->setTurnOffBranding($turnOffBranding);

        $addressHomePage = isset($data['addressHomePage']) ? 1 : 0;
        $entity->setAddressHomePage($addressHomePage);

        $addressSubPage = isset($data['addressSubPage']) ? 1 : 0;
        $entity->setAddressSubPage($addressSubPage);

        $addressIconPage = isset($data['addressIconPage']) ? 1:0;
        $entity->setAddressIconPage($addressIconPage);

        $phoneHomePage = isset($data['phoneHomePage']) ? 1:0;
        $entity->setPhoneHomePage($phoneHomePage);

        $phoneSubPage = isset($data['phoneSubPage']) ? 1:0;
        $entity->setPhoneSubPage($phoneSubPage);

        $phoneDisplay = isset($data['phoneDisplay']) ? 1:0;
        $entity->setPhoneDisplay($phoneDisplay);

        $em->persist($entity);
        $em->flush();

    }


} 