<?php

namespace Appstore\Bundle\MedicineBundle\Importer;
use Appstore\Bundle\MedicineBundle\Entity\MedicineImport;
use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Repository\MedicineParticularRepository;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;
use Setting\Bundle\ToolBundle\Repository\ProductUnitRepository;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class MedicineStockImport
{
    use ContainerAwareTrait;

    private $data = array();
    private $cache = array();

    /* @var $medicineImport  MedicineImport */
    protected $medicineImport;

    public function isValid($data) {

		return true;

	}
    public function setMedicineImport($medicineImport)
    {
        $this->medicineImport = $medicineImport;
    }

    public function import($data)
    {
        $this->data = $data;
        foreach($this->data as $key => $item) {
            $name = "{$item['MedicineName']}";
            $ProductId = isset($item['ProductId']) ? $item['ProductId']:'';
            $brand  = $this->medicineImport->getName();
            $config = $this->medicineImport->getMedicineConfig();
            $exist = $this->getDoctrain()->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig' => $config,'barcode' => $ProductId));
            $stock = isset($item['Stock']) ? $item['Stock']:0;
            $MRP = isset($item['MRP']) ? $item['MRP']:0;
            $PurchasePrice = isset($item['PurchasePrice']) ? $item['PurchasePrice']:0;
            $minStock = isset($item['MinStock']) ? $item['MinStock']:1;
            $Category = isset($item['Category']) ? $item['Category']:'';
            $Brand = isset($item['BrandName']) ? $item['BrandName']:'';
            $Unit = isset($item['Unit']) ? $item['Unit']:'Pcs';

            if($exist) {
                $medicine = $exist;
                $medicine->setOpeningQuantity($stock);
                $medicine->setRemainingQuantity($stock);
                $medicine->setPurchasePrice($PurchasePrice);
                $medicine->setAveragePurchasePrice($PurchasePrice);
                $medicine->setAverageSalesPrice($MRP);
                $medicine->setSalesPrice($MRP);
                $this->persist( $medicine );
                $this->flush();

            }else{

                $medicine = new MedicineStock();
                $medicine->setMedicineConfig($config);
                $medicine->setBrandName(ucfirst(strtolower($brand)));
                $medicine->setName(ucfirst(strtolower($name)));
                $medicine->setBarcode($ProductId);
                $medicine->setOpeningQuantity($stock);
                $medicine->setRemainingQuantity($stock);
                $medicine->setMinQuantity($minStock);
                $medicine->setBrandName($Brand);
                $medicine->setPurchasePrice($PurchasePrice);
                $medicine->setAveragePurchasePrice($PurchasePrice);
                $medicine->setAverageSalesPrice($MRP);
                $medicine->setSalesPrice($MRP);
                if ($Category) {
                    $cat = $this->getcategory(ucfirst(strtolower($Category)));
                    $medicine->setCategory($cat);
                }
                if ($Unit) {
                    $punit = $this->getProductUnit(ucfirst(strtolower($Unit)));
                    $medicine->setUnit($punit);
                }
                $this->persist( $medicine );
                $this->flush();
            }

        }
    }

    private function getcategory($item)
    {
        $config = $this->medicineImport->getMedicineConfig();
        $categoryRepository = $this->getMedicineParticularRepository();

        $size = $categoryRepository->findOneBy(array(
            'medicineConfig'   => $config,
            'name'              => $item
        ));
        if($size){
            return $size;
        }else{
            $size = new MedicineParticular();
            $size->setMedicineConfig($config);
            $size->setName($item);
            $size = $this->save($size);
            return $size;
        }

    }

    private function getProductUnit($item)
    {

        $unit = $this->getProductUnitRepository();
        $size = $unit->findOneBy(array(
            'name'              => $item
        ));
        if($size){
            return $size;
        }else{
            $size = new ProductUnit();
            $size->setName($item);
            $size = $this->save($size);
            return $size;
        }

    }

    /**
     * @return MedicineParticularRepository
     */
    private function getMedicineParticularRepository()
    {
        return $this->getDoctrain()->getRepository(MedicineParticular::class);
    }

    /**
     * @return ProductUnitRepository
     */
    private function getProductUnitRepository()
    {
        return $this->getDoctrain()->getRepository(ProductUnit::class);
    }



    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getEntityManager()
    {
        return $this->getDoctrain()->getManager();
    }



    private function save($entity){
        $this->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function persist($entity){
        $this->getEntityManager()->persist($entity);
    }

    private function getCachedData($type, $key)
    {
        if(isset($this->cache[$type][$key])){
            return $this->cache[$type][$key];
        }

        return NULL;
    }

    private function setCachedData($type, $key, $value)
    {
        $this->cache[$type][$key] = $value;
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

    function sentence_case($string) {
        $sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        $new_string = '';
        foreach ($sentences as $key => $sentence) {
            $new_string .= ($key & 1) == 0?
                ucfirst(strtolower(trim($sentence))) :
                $sentence.' ';
        }
        return trim($new_string);
    }


}