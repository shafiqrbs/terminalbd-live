<?php

namespace Appstore\Bundle\InventoryBundle\Importer;

use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class SalesExcel
{
    use ContainerAwareTrait;

    protected $salesImport;
    private $data = array();

    public function import($data)
    {
        $this->data = $data;
        $config = $this->salesImport->getInventoryConfig();
        $sales = new Sales();
        $sales->setSalesImport($this->salesImport);
        $sales->setInventoryConfig($config);
        $customer = $this->getDoctrain()->getRepository('DomainUserBundle:Customer')->defaultCustomer($config->getGlobalOpton());
        $sales->setCustomer($customer);
        $transactionMethod = $this->getDoctrain()->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $sales->setTransactionMethod($transactionMethod);
        $sales->setSalesMode($config->getDeliveryProcess());
        $sales->setProcess('In-progress');
        $sales->setPaymentStatus('Paid');
        $this->persist($sales);

        foreach($this->data as $key => $item) {

            $barcode = $item['Barcode'];
            $purchaseItem = $this->getDoctrain()->getRepository('InventoryBundle:PurchaseItem')->findOneBy(array('barcode' => $barcode));

            if(!empty($purchaseItem)){
                $salesItem = new SalesItem();
                $salesItem->setSales($sales);
                $salesItem->setItem($purchaseItem->getItem());
                $salesItem->setPurchaseItem($purchaseItem);
                $salesItem->setQuantity($item['Quantity']);
                $salesItem->setPurchasePrice($purchaseItem->getPurchasePrice());
                $salesItem->setEstimatePrice($purchaseItem->getPurchasePrice());
                $salesItem->setSalesPrice($item['UnitPrice']);
                $salesItem->setSubTotal($item['SubTotal']);
                $this->persist($salesItem);
                $this->flush();
            }

        }

    }

    public function setSalesImport($salesImport)
    {
        $this->salesImport = $salesImport;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getEntityManager()
    {
        return $this->getDoctrain()->getManager();
    }


    private function persist($entity){
        $this->getEntityManager()->persist($entity);
    }

    private function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private function getDoctrain()
    {
        return $this->container->get('doctrine');
    }


}