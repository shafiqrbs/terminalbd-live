<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 10/9/15
 * Time: 8:05 AM
 */

namespace Setting\Bundle\ToolBundle\Repository;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\SmsSender;

class SmsSenderRepository extends EntityRepository {

    public function insertEcommerceSenderSms(GlobalOption $globalOption , $customerMobile , $remark, $process, $status)
    {

        $entity = new SmsSender();
        $entity->setMobile($customerMobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('E-commerce for'.$process);
        $entity->setReceiver('Customer');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function insertAdminEcommerceSenderSms(GlobalOption $globalOption , $administratorMobile , $remark, $process, $status)
    {

        $entity = new SmsSender();
        $entity->setMobile($administratorMobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('E-commerce:'.$process);
        $entity->setReceiver('Admin');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function insertEcommerceOrderConfirmSms(GlobalOption $globalOption ,$mobile, $remark , $process, $status)
    {


        $entity = new SmsSender();
        $entity->setMobile($mobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('E-commerce'.$process);
        $entity->setReceiver('Customer');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }


    public function insertEcommerceOrderCommentSms(GlobalOption $globalOption ,$mobile, $remark , $process,$status)
    {

        $entity = new SmsSender();
        $entity->setMobile($mobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('E-commerce'.$process);
        $entity->setReceiver('Customer');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }


    public function insertAdminEcommercePaymentSms(GlobalOption $globalOption , $mobile, $remark , $process, $status)
    {


        $entity = new SmsSender();
        $entity->setMobile($mobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('E-commerce Payment For'.$process);
        $entity->setReceiver('Customer');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function insertAdminEcommercePaymentConfirmSms(GlobalOption $globalOption , $mobile, $remark ,$process, $status)
    {


        $entity = new SmsSender();
        $entity->setMobile($mobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('E-commerce Payment for'.$process);
        $entity->setReceiver('Customer');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }



    public function insertSalesSenderSms(Sales $sales,$status)
    {
        $globalOption = $sales->getInventoryConfig()->getGlobalOption();
        $remark = 'Invoice no '.$sales->getInvoice().', Amount BDT. '.$sales->getTotal().', Method- '.$sales->getTransactionMethod()->getName();

        $entity = new SmsSender();
        $entity->setMobile($sales->getCustomer()->getMobile());
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('Sales');
        $entity->setReceiver('Customer');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }



    public function insertAdminSalesSenderSms(Sales $sales,$administratorMobile,$status)
    {
        $globalOption = $sales->getInventoryConfig()->getGlobalOption();

        $remark = 'Invoice no '.$sales->getInvoice().', Amount BDT. '.$sales->getTotal().', Process- '.$sales->getProcess();

        $entity = new SmsSender();
        $entity->setMobile($administratorMobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('Sales');
        $entity->setReceiver('Admin');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function insertAdminSalesConfirmSms(Sales $sales,$administratorMobile,$status)
    {
        $globalOption = $sales->getInventoryConfig()->getGlobalOption();

        $remark = 'Invoice no '.$sales->getInvoice().', Amount BDT. '.$sales->getTotal().', Process- '.$sales->getProcess();

        $entity = new SmsSender();
        $entity->setMobile($administratorMobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('Sales');
        $entity->setReceiver('Admin');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function insertSalesCourierSms(Sales $sales,$status)
    {
        $globalOption = $sales->getInventoryConfig()->getGlobalOption();

        $remark = 'Invoice no '.$sales->getInvoice().', Amount BDT. '.$sales->getTotal().', Process- '.$sales->getProcess().'/'.$sales->getCourierInvoice();

        $entity = new SmsSender();
        $entity->setMobile($sales->getCustomer()->getMobile());
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('Sales');
        $entity->setReceiver('Customer');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function insertSmsBulk($globalOption,$mobile ,$status){

        $entity = new SmsSender();
        $entity->setMobile($mobile);
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('Bulk Sms');
        $entity->setReceiver('Customer');
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function insertDmsInvoiceSenderSms(DmsInvoice $invoice,$status)
    {
        $globalOption = $invoice->getDmsConfig()->getGlobalOption();

        $remark = 'Invoice no '.$invoice->getInvoice().', Amount BDT. '.$invoice->getTotal().', Process- '.$invoice->getProcess();
        $entity = new SmsSender();
        $entity->setMobile($invoice->getCustomer()->getMobile());
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('Dental Sales');
        $entity->setReceiver('Customer');
        $entity->setRemark($remark);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }



    public function insertLoginSms(User $user, $msg ,$status)
    {
        $entity = new SmsSender();
        $entity->setMobile($user->getUsername());
        $entity->setGlobalOption($user->getGlobalOption());
        $entity->setStatus($status);
        $entity->setProcess('Notification');
        $entity->setReceiver('Customer');
        $entity->setRemark('OTP');
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($user->getGlobalOption());
        }
    }

    public function insertInvoiceSms(Customer $customer, $msg ,$status)
    {
        $entity = new SmsSender();
        $entity->setMobile($customer->getMobile());
        $entity->setGlobalOption($customer->getGlobalOption());
        $entity->setStatus($status);
        $entity->setProcess('Payment Invoice');
        $entity->setReceiver('Customer');
        $entity->setRemark($msg);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($customer->getGlobalOption());
        }
    }


    public function insertHotelInvoiceSenderSms(HotelInvoice $invoice,$status)
	{
		$globalOption = $invoice->getHotelConfig()->getGlobalOption();

		$remark = 'Invoice no '.$invoice->getInvoice().', Amount BDT. '.$invoice->getTotal().', Process- '.$invoice->getProcess();
		$entity = new SmsSender();
		$entity->setMobile($invoice->getCustomer()->getMobile());
		$entity->setGlobalOption($globalOption);
		$entity->setStatus($status);
		$entity->setProcess($invoice->getProcess());
		$entity->setReceiver('Customer');
		$entity->setRemark($remark);
		$this->_em->persist($entity);
		$this->_em->flush();
		if($status == 'success'){
			$this->totalSendSms($globalOption);
		}
	}

    public function insertCustomerSenderSms(Customer $customer , $message, $status)
    {

        $entity = new SmsSender();
        $entity->setMobile($customer->getMobile());
        $entity->setGlobalOption($customer->getGlobalOption());
        $entity->setStatus($status);
        $entity->setReceiver('Customer');
        $entity->setRemark($message);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($customer->getGlobalOption());
        }
    }

    public function insertCustomerOutstandingSms(Customer $customer , $message, $status)
    {

        $entity = new SmsSender();
        $entity->setMobile($customer->getMobile());
        $entity->setGlobalOption($customer->getGlobalOption());
        $entity->setStatus($status);
        $entity->setReceiver('Customer Outstanding');
        $entity->setRemark($message);
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($customer->getGlobalOption());
        }
    }


    public function totalSendSms($globalOption){

        $totalSms = $this->_em->getRepository('SettingToolBundle:SmsSenderTotal')->findOneBy(array('globalOption' => $globalOption));
        if($totalSms and $totalSms->getRemaining()){
            $totalSms->setRemaining($totalSms->getRemaining()-1);
            $totalSms->setSending($totalSms->getSending()+1);
            $this->_em->persist($totalSms);
            $this->_em->flush();
        }

    }
} 