<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionExpense;
use Appstore\Bundle\RestaurantBundle\Entity\Purchase;
use Appstore\Bundle\RestaurantBundle\Entity\PurchaseItem;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantDamage;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantStockHistory;
use Doctrine\ORM\EntityRepository;


/**
 * ParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RestaurantStockHistoryRepository extends EntityRepository
{

    public function getItemOpeningQuantity(Particular $item){
        $em = $this->_em;
        $qb = $this->createQueryBuilder('e');
        $qb->select('(COALESCE(SUM(e.quantity),0) AS openingQuantity');
        $qb->where("e.item = :item")->setParameter('item', $item->getId());
        $result = $qb->getQuery()->getSingleResult();
        $openingQuantity = $result['openingQuantity'];
        return $openingQuantity;

    }

    public function updateItemClosingQuantity(RestaurantStockHistory $stock){
        $em = $this->_em;
        $closingQnt = ($stock->getOpeningQuantity() + $stock->getQuantity());
        $stock->setClosingQuantity($closingQnt);
        $em->persist($stock);
        $em->flush();

    }

    public function processStockQuantity($item , $fieldName = ''){

        $em = $this->_em;

        $openingQnt = $this->getItemOpeningQuantity($item->getParticular());

        $entity = new RestaurantStockHistory();

        if($fieldName == 'purchase'){

            /* @var $item PurchaseItem */

            $exist = $this->findOneBy(array('purchaseItem' => $item));
            if($exist){ $entity = $exist; }
            $entity->setQuantity($item->getQuantity());
            $entity->setPurchaseQuantity($item->getQuantity());
            $entity->setItem($item->getParticular());
            $entity->setPurchaseItem($item);
            $entity->setProcess('purchase');


        }elseif($fieldName == 'purchase-return') {

            /* @var $item BusinessPurchaseReturnItem */

            $entity->setQuantity("-{$item->getQuantity()}");
            $entity->setPurchaseReturnQuantity($item->getQuantity());
            $entity->setItem($item->getParticular());
            $entity->setPurchaseReturnItem($item);
            $entity->setProcess('purchase-return');

        }elseif($fieldName == 'sales'){

            /* @var $item InvoiceParticular */

            $entity->setQuantity("-{$item->getQuantity()}");
            $entity->setSalesQuantity($item->getQuantity());
            $entity->setItem($item->getParticular());
            $entity->setSalesItem($item);
            $entity->setProcess('sales');

        }elseif($fieldName == 'sales-return'){

            /* @var $item BusinessInvoiceReturn */

            $entity->setQuantity($item->getQuantity());
            $entity->setSalesQuantity($item->getQuantity());
            $entity->setItem($item->getParticular());
            $entity->setSalesItem($item);
            $entity->setProcess('sales-return');

        }elseif($fieldName == 'damage') {

            /* @var $item RestaurantDamage */

            $entity->setQuantity("-{$item->getQuantity()}");
            $entity->setDamageQuantity($item->getQuantity());
            $entity->setItem($item->getParticular());
            $entity->setDamageItem($item);
            $entity->setProcess('sales-return');

        }elseif($fieldName == 'production') {

            /* @var $item RestaurantDamage */

            $entity->setQuantity("-{$item->getQuantity()}");
            $entity->setProductionQuantity($item->getQuantity());
            $entity->setItem($item->getParticular());
            $entity->setProductionExpense($item);
            $entity->setProcess('production');

        }
        if($openingQnt){
            $entity->setOpeningQuantity(floatval($openingQnt));
        }else{
            $entity->setOpeningQuantity(0);
        }
        $closingQuantity = $entity->getQuantity() + $entity->getOpeningQuantity();
        $entity ->setClosingQuantity(floatval($closingQuantity));
        $entity->setRestaurantConfig($item->getParticular()->getRestaurantConfig());
        $em->persist($entity);
        $em->flush();

    }

    public function processInsertPurchaseItem(Purchase $entity){

        $em = $this->_em;

        /** @var $item Purchase  */

        if($entity->getPurchaseItems()){

            foreach($entity->getPurchaseItems() as $item ){
                $em->createQuery("DELETE RestaurantBundle:RestaurantStockHistory e WHERE e.purchaseItem = '{$item->getId()}'")->execute();
                $this->processStockQuantity($item,"purchase");
            }
        }
    }

    public function processInsertDamageItem(RestaurantDamage $entity){

        $em = $this->_em;
        $em->createQuery("DELETE RestaurantBundle:RestaurantStockHistory e WHERE e.damage = '{$entity->getId()}'")->execute();
        $this->processStockQuantity($entity,"damage");
    }

    public function processReversePurchaseItem(Purchase $entity){

        $em = $this->_em;

        /** @var $item Purchase  */

        if($entity->getPurchaseItems()){
            foreach($entity->getPurchaseItems() as $item ){
                $em->createQuery("DELETE RestaurantBundle:RestaurantStockHistory e WHERE e.purchaseItem = '{$item->getId()}'")->execute();
            }
        }
    }

    public function processInsertPurchaseReturnItem(BusinessPurchaseReturn $entity){

        $em = $this->_em;

        /** @var $item BusinessPurchaseReturnItem  */

       /* if($entity->getBusinessPurchaseReturnItems()){

            foreach($entity->getBusinessPurchaseReturnItems() as $item ){
                $em->createQuery("DELETE BusinessBundle:RestaurantStockHistory e WHERE e.purchaseReturnItem = '{$item->getId()}'")->execute();
                if($item->getQuantity() > 0){
                    $this->processStockQuantity($item,"purchase-return");
                }
            }
        }*/
    }

    public function processInsertSalesItem(Invoice $entity){

        $em = $this->_em;

        /** @var $item InvoiceParticular  */

        if($entity->getInvoiceParticulars()){

            foreach($entity->getInvoiceParticulars() as $item ){
                $em->createQuery("DELETE RestaurantBundle:RestaurantStockHistory e WHERE e.salesItem = '{$item->getId()}'")->execute();
                if( $item->getParticular()->getService()->getSlug() == 'stockable' and $item->getQuantity() > 0 ){
                    $this->processStockQuantity($item,"sales");
                }
            }
        }
    }

    public function processInsertProductionItem(ProductionExpense $entity){

        $em = $this->_em;
        $em->createQuery("DELETE RestaurantBundle:RestaurantStockHistory e WHERE e.productionExpense = '{$entity->getId()}'")->execute();
        $this->processStockQuantity($entity,"production");

    }

    public function processReverseSalesItem(Invoice $entity){

        $em = $this->_em;
        /** @var $item InvoiceParticular  */
        if($entity->getInvoiceParticulars()){
            foreach($entity->getInvoiceParticulars() as $item ){
                $em->createQuery("DELETE RestaurantBundle:RestaurantStockHistory e WHERE e.salesItem = '{$item->getId()}'")->execute();
            }
        }
    }



    public function processInsertSalesReturnItem(BusinessInvoice $entity){

        $em = $this->_em;

        /** @var $item BusinessInvoiceReturn  */

        /*if($entity->getBusinessInvoiceParticulars()){

            foreach($entity->getBusinessInvoiceParticulars() as $item ){
                $em->createQuery("DELETE BusinessBundle:RestaurantStockHistory e WHERE e.salesReturnItem = '{$item->getId()}'")->execute();
                if($item->getQuantity() > 0){
                    $this->processStockQuantity($item,"sales-return");
                }
            }
        }*/
    }



    public function openingDailyQuantity(RestaurantConfig $config,$data)
    {

        $item = isset($data['name'])? $data['name'] :'';
        if(isset($data['startDate'])){
            $date = new \DateTime($data['startDate']);
        }else{
            $date = new \DateTime();
        }
        $date->add(\DateInterval::createFromDateString('yesterday'));
        $tillDate = $date->format('Y-m-d 23:59:59');
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) as openingQnt');
        $qb->join("e.item",'i');
        $qb->where("i.name = :name")->setParameter('name',$item);
        $qb->andWhere("e.created <= :created")->setParameter('created', $tillDate);
        $lastCode = $qb->getQuery()->getOneOrNullResult()['openingQnt'];
        if (empty($lastCode)) {
            return '';
        }
        return $lastCode;

    }

    public function monthlyStockLedger(RestaurantConfig $config , $data)
    {
        $config = $config->getId();
        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;
        $item = isset($data['name'])? $data['name'] :'';
        $sql = "SELECT DATE_FORMAT(e.created,'%d-%m-%Y') as date ,COALESCE(SUM(e.purchaseQuantity),0) as purchaseQuantity,COALESCE(SUM(e.purchaseReturnQuantity),0) as purchaseReturnQuantity,COALESCE(SUM(e.salesQuantity),0) as salesQuantity,COALESCE(SUM(e.salesReturnQuantity),0) as salesReturnQuantity,COALESCE(SUM(e.damageQuantity),0) as damageQuantity
                FROM restaurant_stock_history as e
                JOIN restaurant_particular ON e.item_id = particular.id
                WHERE e.restaurantConfig_id = :config AND particular.name = :item  AND MONTHNAME(e.created) =:month AND YEAR(e.created) =:year
                GROUP BY date";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('item', "{$item}");
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $results =  $stmt->fetchAll();
        $arrays = [];
        foreach ($results as $result){
            $arrays[$result['date']] = $result;
        }
        return $arrays;
    }

}
