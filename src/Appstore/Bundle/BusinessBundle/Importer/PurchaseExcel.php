<?php

namespace Appstore\Bundle\BusinessBundle\Importer;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\Category;
use Appstore\Bundle\BusinessBundle\Entity\ItemImport;
use Appstore\Bundle\BusinessBundle\Repository\CategoryRepository;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class PurchaseExcel
{
    use ContainerAwareTrait;

    /* @var $itemImport  ItemImport */
    protected $itemImport;


    private $data = array();

    public function isValid($data) {

        return true;

    }

    public function import($data)
    {
        $this->data = $data;
        $inventory = $this->itemImport->getBusinessConfig();
        foreach($this->data as $key => $item) {
            $name = ucfirst(strtolower(trim($item['Name'])));
            $category = ucfirst(strtolower(trim($item['Category'])));
            $productOld = $this->getDoctrain()->getRepository('BusinessBundle:BusinessParticular')->findOneBy(array('businessConfig' => $inventory,'name' => $name));
            if(empty($productOld)){
                $salesPrice = empty($item['SalesPrice']) ? 0 : $item['SalesPrice'];
                $purchasePrice = empty($item['PurchasePrice']) ? 0 : $item['PurchasePrice'];
                $unit = empty($item['Unit']) ? 'Pcs' : $item['Unit'];
                $quantity = empty($item['OpeningQuantity']) ? 0 : trim($item['OpeningQuantity']);
                $product = new BusinessParticular();
                $product->setBusinessConfig($inventory);
                $product->setName($name);
                $unit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $unit));
                if(!empty($unit)){
                    $product->setUnit($unit);
                }
                $type = $this->getDoctrain()->getRepository('BusinessBundle:BusinessParticularType')->find(2);
                if(!empty($type)){
                    $product->setBusinessParticularType($type);
                }
                $product->setSalesPrice($salesPrice);
                $product->setPurchasePrice($purchasePrice);
                $product->setOpeningQuantity($quantity);
                $product->setRemainingQuantity($product->getOpeningQuantity());
                if ($category) {
                    $cat = $this->getCategory($category);
                    $product->setCategory($cat);
                }
                $this->save($product);

            }else{

                $product = $productOld;
                $salesPrice = floatval(empty($item['SalesPrice']) ? 0 : $item['SalesPrice']);
                $purchasePrice = floatval(empty($item['PurchasePrice']) ? 0 : $item['PurchasePrice']);
                $quantity = (int) empty($item['OpeningQuantity']) ? 0 : trim($item['OpeningQuantity']);
                $product->setSalesPrice($salesPrice);
                $product->setPurchasePrice($purchasePrice);
                $product->setOpeningQuantity($quantity);
                $product->setRemainingQuantity($product->getOpeningQuantity());
                $this->save($product);
            }

        }

    }

    public function setItemImport($itemImport)
    {
        $this->itemImport = $itemImport;
    }


    private function getCategory($item)
    {
        $config = $this->itemImport->getBusinessConfig();
        $categoryRepository = $this->getCategoryRepository();

        $category = $categoryRepository->findOneBy(array(
            'businessConfig'   => $config,
            'name'              => $item
        ));
        if($category){
            return $category;
        }else{
            $category = new Category();
            $category->setName($item);
            $category->setBusinessConfig($config);
            $category = $this->save($category);
            return $category;
        }
    }

    private function save($entity){
        $this->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    public function setExcelImport($excelImport)
    {
        $this->excelImport = $excelImport;
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

    /**
     * @return CategoryRepository
     */
    private function getCategoryRepository()
    {
        return $this->getDoctrain()->getRepository('BusinessBundle:Category');
    }


}