<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 10/9/15
 * Time: 8:05 AM
 */

namespace Setting\Bundle\ToolBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail;
use Setting\Bundle\ToolBundle\Entity\SmsSenderTotal;


class SmsSenderTotalRepository extends EntityRepository {

    public function updateSmsBundle(InvoiceSmsEmail $entity)
    {
        $sms = $this->_em->getRepository('SettingToolBundle:SmsSenderTotal')->findOneBy(array('globalOption'=> $entity->getGlobalOption()));
        if(empty($sms)){
            $sms = new SmsSenderTotal();
            $sms->setGlobalOption($entity->getGlobalOption());
            $sms->setPurchase($entity->getSmsQuantity());
            $sms->setRemaining($entity->getSmsQuantity());
        }else{
            $purchase = $sms->getPurchase();
            $sms->setPurchase($purchase + $entity->getSmsQuantity());
            $sms->setRemaining($purchase + $entity->getSmsQuantity() - $sms->getSending());
        }
        $this->_em->persist($sms);
        $this->_em->flush();

        $sendSms = $this->_em->getRepository('SettingToolBundle:SmsSender')->findBy(array('globalOption'=> $entity->getGlobalOption()));
        if(!empty($sendSms)){
            foreach($sendSms as $send){
                $this->_em->remove($send);
                $this->_em->flush();
            }

        }


    }
} 