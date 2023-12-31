<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * MedicineSalesItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineSalesItemRepository extends EntityRepository
{


    public  function reportSalesItemPurchaseSalesOverview(User $user, $data = array()){

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('si');
        $qb->join('si.medicineSales','s');
        $qb->join("si.medicineStock",'m');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('COUNT(si.id) AS totalItem');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice) AS totalPurchase');
        $qb->addSelect('SUM(si.quantity * si.salesPrice) AS totalSales');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }



    public function getInvoicePurchasePrice($entity)
    {
        $sql = "SELECT COALESCE(SUM(salesItem.quantity * salesItem.purchasePrice),0) as total FROM medicine_sales_item as salesItem
                WHERE salesItem.medicineSales_id = :invoice";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('invoice', $entity);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result['total'];
    }

    public function findCurrentShortListCount($user)
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('item');
        $qb->join('item.medicineSales','s');
        $qb->select('COUNT(item.id) as counts');
        $qb->where("s.medicineConfig = :config")->setParameter('config', $config);
        $qb->andWhere("item.isShort = 0");
        $count = $qb->getQuery()->getSingleScalarResult();
        return  $count;
    }

    public function topSalesItem(User $user,$data)
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $compare = new \DateTime('now');
        $createdStart = isset($data['startDate'])? $data['startDate'] :$compare->format('Y-m-d');
        $createdEnd = isset($data['endDate'])? $data['endDate'] :$compare->format('Y-m-t');
        $brandName = isset($data['brandName'])? $data['brandName'] :'';
        $topLimit = (isset($data['topLimit']) and $data['topLimit'] > 1) ? $data['topLimit'] :10;
        $qb = $this->createQueryBuilder('si');
        $qb->select('SUM(si.quantity) as salesQuantity');
        $qb->addSelect('ms.id as stockId','ms.name as name','ms.brandName as brandName','ms.remainingQuantity as remain','ms.purchaseQuantity as purchaseQuantity','ms.minQuantity as minQty');
        $qb->join('si.medicineStock','ms');
        $qb->join('si.medicineSales','s');
        $qb->where('s.medicineConfig = :config')->setParameter('config', $config) ;
        if($brandName){
            $qb->andWhere($qb->expr()->like("ms.brandName", "'%$brandName%'"  ));
        }
        if (!empty($createdStart)) {
            $compareTo = new \DateTime($createdStart);
            $created =  $compareTo->format('Y-m-d 00:00:00');
            $qb->andWhere("s.created >= :createdStart");
            $qb->setParameter('createdStart', $created);
        }
        if (!empty($createdEnd)) {
            $compareTo = new \DateTime($createdEnd);
            $createdEnd =  $compareTo->format('Y-m-d 23:59:59');
            $qb->andWhere("s.created <= :createdEnd");
            $qb->setParameter('createdEnd', $createdEnd);
        }
        $qb->groupBy('ms.id');
        $qb->orderBy('salesQuantity','DESC');
        $qb->setMaxResults($topLimit);
        $result = $qb->getQuery()->getArrayResult();
        return  $result;
    }

    public function slowSalesItem(User $user,$data)
    {

        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $compare = new \DateTime('now');
        $createdStart = isset($data['startDate'])? $data['startDate'] :$compare->format('Y-m-d');
        $createdEnd = isset($data['endDate'])? $data['endDate'] :$compare->format('Y-m-t');
        $brandName = isset($data['brandName'])? $data['brandName'] :'';
        $topLimit = (isset($data['topLimit']) and $data['topLimit'] > 1) ? $data['topLimit'] :10;
        $brand = "";
        if($brandName){
            $brand = "AND stock.brandName = :brandName";
        }
        $sql = "SELECT stock.name as name, stock.id as stockId,stock.brandName as brandName,stock.remainingQuantity as remain,stock.purchaseQuantity as purchaseQuantity,stock.salesQuantity as salesQuantity,stock.minQuantity as minQty 
                FROM medicine_stock as stock
                WHERE stock.medicineConfig_id = :config {$brand} AND stock.remainingQuantity > {$topLimit}  AND stock.id NOT IN  (SELECT s.id as id FROM medicine_sales_item as salesItem
             JOIN medicine_sales as sales ON salesItem.medicineSales_id = sales.id 
             JOIN medicine_stock as s ON salesItem.medicineStock_id = s.id                                                                         
             WHERE sales.medicineConfig_id = :config AND sales.process = :process AND sales.created >= :startDate AND sales.created <= :endDate
             GROUP BY medicineStock_id)  ORDER BY remain DESC";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'Done');
        $stmt->bindValue('startDate', $createdStart);
        $stmt->bindValue('endDate', $createdEnd);
        if($brandName) {
            $stmt->bindValue('brandName', $brandName);
        }
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return  $result;
    }

    public function salesStockItemUpdate(MedicineStock $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.medicineStock = :stock')->setParameter('stock', $stockItem->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function salesPurchaseStockItemUpdate(MedicinePurchaseItem $item)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.medicinePurchaseItem = :purchaseItem')->setParameter('purchaseItem', $item->getId());
        $qb->andWhere('e.medicineStock = :medicineStock')->setParameter('medicineStock', $item->getMedicineStock()->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return !empty($qnt['quantity']) ? $qnt['quantity'] : 0;
    }


    public function insertCopyItems($entity, MedicineSales $sales)
    {
        $em = $this->_em;
        /* @var $item MedicineSalesItem */
        foreach ($sales->getMedicineSalesItems() as $item){

            $purchaseItem = new MedicineSalesItem();
            $purchaseItem->setMedicineSales($entity);
            $purchaseItem->setMedicineStock($item->getMedicineStock());
            $purchaseItem->setQuantity($item->getQuantity());
            $purchaseItem->setPurchasePrice($item->getMedicineStock()->getPurchasePrice());
            $purchaseItem->setSalesPrice($item->getSalesPrice());
            $purchaseItem->setDiscountPrice($item->getDiscountPrice());
            $purchaseItem->setSubTotal($item->getSubTotal());
            $em->persist($purchaseItem);
            $em->flush();
        }
    }

    public function temporarySalesInsert(User $user, MedicineSales $sales)
    {
        $em = $this->_em;
        $entities = $user->getMedicineSalesTemporary();
        $stockIds = array();
        foreach ($entities as $item) {
            $stockIds[] = $item->getMedicineStock()->getId();
            /* @var  $item MedicineSalesTemporary */
            $entity = new MedicineSalesItem();
            $entity->setMedicineSales( $sales );
            $entity->setMedicineStock( $item->getMedicineStock() );
            $entity->setQuantity($item->getQuantity() );
            $entity->setMrpPrice($item->getMedicineStock()->getSalesPrice());
            $entity->setItemPercent( $item->getItemPercent() );
            $entity->setSalesPrice( $item->getSalesPrice() );
            $entity->setSubTotal($item->getSubTotal());
            $entity->setPurchasePrice( $item->getPurchasePrice() );
            $entity->setIsShort($item->isShort());
            if($item->getMedicinePurchaseItem()) {
                $entity->setMedicinePurchaseItem( $item->getMedicinePurchaseItem() );
            }
            if ( $sales->getDiscountType() == 'percentage' ) {
                $entity->setDiscountPrice( $this->itemDiscountPrice( $sales, $item->getSalesPrice() ) );
            } else {
                $entity->setDiscountPrice( $item->getSalesPrice() );
            }
            $em->persist($entity);
            if($item->getMedicinePurchaseItem()) {
                $em->getRepository( 'MedicineBundle:MedicinePurchaseItem' )->updateRemovePurchaseItemQuantity( $item->getMedicinePurchaseItem(), 'sales' );
            }
            //$em->getRepository( 'MedicineBundle:MedicineStock' )->updateRemovePurchaseQuantity( $item->getMedicineStock(), 'sales' );
        }
        $em->flush();
        $array = implode(",",$stockIds);
        $sqlStockRemin = "UPDATE medicine_stock as stock
            inner join (
              SELECT ele.medicineStock_id, ROUND(COALESCE(SUM(ele.quantity),0),2) as salesQuantity
              FROM medicine_sales_item as ele 
              WHERE ele.medicineStock_id IN ({$array})
              GROUP BY ele.medicineStock_id
            ) as pa on stock.id = pa.medicineStock_id
  SET stock.remainingQuantity = ((COALESCE(stock.openingQuantity,0) + COALESCE(stock.purchaseQuantity,0) + COALESCE(stock.salesReturnQuantity,0)+ COALESCE(stock.bonusQuantity,0)+ COALESCE(stock.bonusAdjustment,0)+ COALESCE(stock.adjustmentQuantity,0)) - (COALESCE(pa.salesQuantity,0) + COALESCE(stock.purchaseReturnQuantity,0) + COALESCE(stock.damageQuantity,0)))
 , stock.salesQuantity = pa.salesQuantity";

        $qb5 = $this->getEntityManager()->getConnection()->prepare($sqlStockRemin);
        $qb5->execute();

        $DoctorInvoice = $em->createQuery('DELETE MedicineBundle:MedicineSalesTemporary e WHERE e.user = '.$user->getId());
        $DoctorInvoice->execute();

    }

    public function temporarySalesInsertManual(User $user, MedicineSales $sales)
    {
        $em = $this->_em;
        $salesId = $sales->getId();
        $userId = $user->getId();

        $stock = $em->createQuery('DELETE MedicineBundle:MedicineSalesItem e WHERE e.medicineSales = '.$salesId);
        if($stock){
            $stock->execute();
        }

        $elem = "INSERT INTO medicine_sales_item (medicineStock_id,quantity,salesPrice,purchaseprice,mrpPrice,discountPrice,subTotal,medicineSales_id)
  SELECT `medicineStock_id`, quantity, salesPrice,purchasePrice,salesPrice as mrp,salesPrice as discount,subTotal,$salesId
  FROM medicine_sales_temporary
  WHERE user_id =:user";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($elem);
        $qb1->bindValue('user', $userId);
        $qb1->execute();

        $sqlStockRemin = "UPDATE medicine_stock as stock
            inner join (
              SELECT ele.medicineStock_id, ROUND(COALESCE(SUM(ele.quantity),0),2) as salesQuantity
              FROM medicine_sales_item as ele 
              WHERE ele.medicineSales_id = {$salesId}
              GROUP BY ele.medicineStock_id
            ) as pa on stock.id = pa.medicineStock_id
  SET stock.remainingQuantity = ((COALESCE(stock.openingQuantity,0) + COALESCE(stock.purchaseQuantity,0) + COALESCE(stock.salesReturnQuantity,0)+ COALESCE(stock.bonusQuantity,0)+ COALESCE(stock.bonusAdjustment,0)+ COALESCE(stock.adjustmentQuantity,0)) - (COALESCE(pa.salesQuantity,0) + COALESCE(stock.purchaseReturnQuantity,0) + COALESCE(stock.damageQuantity,0)))
 , stock.salesQuantity = pa.salesQuantity";

        $qb5 = $this->getEntityManager()->getConnection()->prepare($sqlStockRemin);
        $qb5->execute();

        $DoctorInvoice = $em->createQuery('DELETE MedicineBundle:MedicineSalesTemporary e WHERE e.user = '.$user->getId());
        $DoctorInvoice->execute();

    }

    public function itemDiscountPrice(MedicineSales $sales,$price)
    {
        $discountPrice = $price;
        if($sales->getDiscountType() == 'percentage'){
            $discount = (($price * $sales->getDiscountCalculation())/100);
            $discountPrice = ($price - $discount);
        }
        return round($discountPrice,2);
    }

    public function insertInstantSalesItem(MedicineSales $sales,MedicinePurchaseItem $item,$data){
        $em = $this->_em;
        $entity = new MedicineSalesItem();
        $entity->setMedicineSales($sales);
        $entity->setMedicineStock($item->getMedicineStock());
        $entity->setMedicinePurchaseItem($item);
        $entity->setQuantity($data['salesQuantity']);
        $entity->setSalesPrice($item->getSalesPrice());
        $entity->setSubTotal($item->getSalesPrice() * $data['salesQuantity']);
        $entity->setPurchasePrice($item->getPurchasePrice());
        $entity->setDiscountPrice($this->itemDiscountPrice($sales,$item->getSalesPrice()));
        $em->persist($entity);
        $em->flush();
        $em->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($item->getMedicinePurchaseItem(),'sales');
        $em->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item->getMedicineStock(),'sales');

    }

	protected function handleSearchBetween($qb,$data)
	{

		$invoice = isset($data['invoice'])? $data['invoice'] :'';
		$transactionMethod = isset($data['transactionMethod'])? $data['transactionMethod'] :'';
		$salesBy = isset($data['salesBy'])? $data['salesBy'] :'';
		$quantity = isset($data['quantity'])? $data['quantity'] :'';
		$process = isset($data['process'])? $data['process'] :'';
		$customer = isset($data['customer'])? $data['customer'] :'';
		$customerMobile = isset($data['mobile'])? $data['mobile'] :'';
		$createdStart = isset($data['startDate'])? $data['startDate'] :'';
		$createdEnd = isset($data['endDate'])? $data['endDate'] :'';
		$medicineName = isset($data['medicineName'])? $data['medicineName'] :'';
        $medicineBrand = isset($data['brandName'])? $data['brandName'] :'';
        $keyword = isset($data['keyword'])? $data['keyword'] :'';
		if (!empty($invoice)) {
			$qb->andWhere($qb->expr()->like("s.invoice", "'%$invoice%'"  ));
		}

		if (!empty($customerMobile)) {
			$qb->join('s.customer','c');
			$qb->andWhere($qb->expr()->like("c.mobile", "'%$customerMobile%'"  ));
		}

		if (!empty($customer)) {
			$qb->join('s.customer','c');
			$qb->andWhere($qb->expr()->like("c.mobile", "'%$customer%'"  ));
		}

		if (!empty($createdStart)) {
			$compareTo = new \DateTime($createdStart);
			$created =  $compareTo->format('Y-m-d 00:00:00');
			$qb->andWhere("s.created >= :createdStart");
			$qb->setParameter('createdStart', $created);
		}

		if (!empty($createdEnd)) {
			$compareTo = new \DateTime($createdEnd);
			$createdEnd =  $compareTo->format('Y-m-d 23:59:59');
			$qb->andWhere("s.created <= :createdEnd");
			$qb->setParameter('createdEnd', $createdEnd);
		}

		if(!empty($salesBy)){
			$qb->join("s.salesBy",'u');
			$qb->andWhere("u.username = :username");
			$qb->setParameter('username', $salesBy);
		}

		if(!empty($process)){
			$qb->andWhere("s.process = :process");
			$qb->setParameter('process', $process);
		}
		if(!empty($transactionMethod)){
			$qb->andWhere("s.transactionMethod = :method");
			$qb->setParameter('method', $transactionMethod);
		}
		if(!empty($medicineName)){
	        $qb->andWhere("m.name = :name")->setParameter('name', $medicineName);
		}
        if(!empty($medicineBrand)){
            $qb->andWhere($qb->expr()->like("m.brandName", "'%$medicineBrand%'"  ));
        }
        if(!empty($quantity)){
            $qb->andWhere("si.quantity = :quantity")->setParameter('quantity', $quantity);
		}

        if (!empty($keyword)) {
            $keyword = $this->clean($keyword);
            $qb->andWhere('m.name LIKE :searchTerm OR m.brandName LIKE :searchTerm OR m.slug LIKE :searchTerm');
            $qb->setParameter('searchTerm', '%'.$keyword.'%');
        }

	}

    public function  clean($string) {
        $res = preg_replace('/[0-9\@\&\^\%\(\)\#\$\!\]\[\}\{\*\'\"\.\;\" "]+/', ' ', $string);
        return $res;

    }



    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        if (!empty($data['startDate']) ) {
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }

    public function salesItemLists(User $user, $data)
    {

        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('si');
        $qb->join('si.medicineSales','s');
        $qb->join("si.medicineStock",'m');
        $qb->where('s.medicineConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.created','DESC');
        if(empty($data)){
            return '';
        }else{
            $res = $qb->getQuery();
            return  $res;
        }
    }

    public function currentShortList($config, $data)
    {
        $qb = $this->createQueryBuilder('si');
        $qb->join('si.medicineSales','s');
        $qb->join("si.medicineStock",'m');
        $qb->where('s.medicineConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('si.isShort = 1');
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.created','DESC');
        $res = $qb->getQuery();
        return  $res;
    }


    public function getSalesItems(MedicineSales $sales)
    {
        $entities = $sales->getMedicineSalesItems();
        $data = '';
        $i = 1;
        /* @var $entity MedicineSalesItem */
        foreach ($entities as $entity) {
            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td class='span1' >{$i}.</td>";
            $data .= "<td class='span4' >{$entity->getMedicineStock()->getName()}</td>";
            $data .= "<td class='span2' >{$entity->getSalesPrice()}</td>";
            $data .= "<td class='span1' >{$entity->getQuantity()}</td>";
            $data .= "<td class='span2' >{$entity->getSubTotal()}</td>";
            $data .= "<td class='span1' >";
            $data .= "<a id='{$entity->getId()}' data-id='{$entity->getId()}'  data-url='/medicine/sales/{$sales->getId()}/{$entity->getId()}/sales-item-delete' href='javascript:' class='btn red mini itemDelete' ><i class='icon-trash'></i></a>";
            $data .= "</td>";
            $data .= "</tr>";
            $i++;
        }
        return $data;
    }


    public function medicineInvoiceParticularReverse(Invoice $invoice)
    {

    }

    public function getLastCode($entity,$datetime)
    {

        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');


        $qb = $this->createQueryBuilder('ip');
        $qb
            ->select('MAX(ip.code)')
            ->join('ip.medicineInvoice','s')
            ->where('s.hospitalConfig = :hospital')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('hospital', $entity->getMedicineConfig())
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }


    public function reportSalesAccessories(GlobalOption $option ,$data)
    {
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        $qb = $this->createQueryBuilder('ip');
        $qb->join('ip.particular','p');
        $qb->join('ip.medicineInvoice','i');
        $qb->select('SUM(ip.quantity * p.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('i.hospitalConfig = :hospital');
        $qb->setParameter('hospital', $option->getMedicineConfig());
        $qb->andWhere("i.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death','Released','Dead'));
        if (!empty($data['startDate']) ) {
            $qb->andWhere("i.updated >= :startDate");
            $qb->setParameter('startDate', $startDate.' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("i.updated <= :endDate");
            $qb->setParameter('endDate', $endDate.' 23:59:59');
        }
        $res = $qb->getQuery()->getOneOrNullResult();
        return $res['totalPurchaseAmount'];

    }


    public function searchAutoComplete(MedicineConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineInvoice', 'i');
        $query->select('e.metaValue as id');
        $query->where($query->expr()->like("e.metaValue", "'$q%'"  ));
        $query->andWhere("i.medicineConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.metaValue');
        $query->orderBy('e.metaValue', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();
    }

    public function searchProcedureDiseasesComplete(MedicineConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineInvoice', 'i');
        $query->select('e.diseases as id');
        $query->where($query->expr()->like("e.diseases", "'$q%'"  ));
        $query->andWhere("i.medicineConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.diseases');
        $query->orderBy('e.diseases', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();
    }

    /* Sales Medicine item */

	public  function reportSalesStockItem(User $user, $data=''){

        $sort = isset($data['sort'])? $data['sort'] :'mds.name';
        $direction = isset($data['direction'])? $data['direction'] :'ASC';
		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $group = isset($data['group']) ? $data['group'] :'medicineStock';

		$qb = $this->createQueryBuilder('si');
		$qb->join('si.medicineSales','s');
		$qb->join('si.medicineStock','mds');
		$qb->select('SUM(si.quantity) AS quantity');
		$qb->addSelect('SUM(si.quantity * si.salesPrice ) AS salesPrice');
		$qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS purchasePrice');
		$qb->addSelect('mds.name AS name','mds.remainingQuantity AS remainingQuantity','mds.brandName AS brandName');
		$qb->addSelect('mds.sku AS sku');
		$qb->where('s.medicineConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('s.process = :process');
		$qb->setParameter('process', 'Done');
		if($group == 'medicinePurchaseItem') {
			$qb->addSelect('item.barcode AS barcode');
		}
		$this->handleSearchStockBetween($qb,$data);
		if ($userBranch){
			$qb->andWhere("s.branches = :branch");
			$qb->setParameter('branch', $userBranch);
		}
		$qb->groupBy('si.'.$group);
        $qb->orderBy("{$sort}",$direction);
		return $qb->getQuery();
	}

    /* Sales Medicine item */

    public  function reportSalesStock(User $user, $data=''){

        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('si');
        $qb->join('si.medicineSales','s');
        $qb->join('si.medicineStock','mds');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('SUM(si.quantity * mds.salesPrice ) AS salesPrice');
        $qb->addSelect('SUM(si.quantity * mds.purchasePrice ) AS purchasePrice');
        $qb->addSelect('mds.name AS name','mds.remainingQuantity AS remainingQuantity','mds.brandName AS brandName');
        $qb->addSelect('mds.sku AS sku');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchStockBetween($qb,$data);
        $qb->orderBy('mds.brandName','ASC');
        $qb->addOrderBy('mds.name','ASC');
        $qb->groupBy('mds.id');
        return $qb->getQuery()->getArrayResult();
    }

	protected function handleSearchStockBetween($qb,$data)
	{

		$createdStart = isset($data['startDate'])? $data['startDate'] :'';
		$createdEnd = isset($data['endDate'])? $data['endDate'] :'';
		$name = isset($data['name'])? $data['name'] :'';
		$sku = isset($data['sku'])? $data['sku'] :'';
		$brandName = isset($data['brandName'])? $data['brandName'] :'';

		if (!empty($name)) {
			$qb->andWhere($qb->expr()->like("mds.name", "'%$name%'"  ));
		}
		if (!empty($sku)) {
			$qb->andWhere($qb->expr()->like("mds.sku", "'%$sku%'"  ));
		}
		if (!empty($brandName)) {
			$qb->andWhere($qb->expr()->like("mds.brandName", "'%$brandName%'"  ));
		}

		if (!empty($createdStart)) {
			$compareTo = new \DateTime($createdStart);
			$created =  $compareTo->format('Y-m-d 00:00:00');
			$qb->andWhere("s.created >= :createdStart");
			$qb->setParameter('createdStart', $created);
		}

		if (!empty($createdEnd)) {
			$compareTo = new \DateTime($createdEnd);
			$createdEnd =  $compareTo->format('Y-m-d 23:59:59');
			$qb->andWhere("s.created <= :createdEnd");
			$qb->setParameter('createdEnd', $createdEnd);
		}

	}

    public function discountPercentList()
    {
        $array = array(

            1=>1,
            2=>2,
            3=>3,
            4=>4,
            5=>5,
            6=>6,
            7=>7,
            8=>8,
            9=>9,
            10=>10,
            11=>11,
            12=>12,
            13=>13,
            14=>14,
            15=>15,
            16=>16,
            17=>17,
            18=>18,
            19=>19,
            20=>20,
            21=>21,
            22=>22,
            23=>23,
            24=>24,
            25=>25,
            26=>26,
            27=>27,
            28=>28,
            29=>29,
            30=>30
        );

        return $array;


    }
}
