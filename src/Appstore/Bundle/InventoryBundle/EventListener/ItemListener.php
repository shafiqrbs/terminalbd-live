<?php

namespace Appstore\Bundle\InventoryBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ItemListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof Item) {

            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode+1);
            $codes = $this->getItemCodes($entity);
            $entity->setName($codes['name']);
            $entity->setSku($codes['sku']);
            $entity->setSkuSlug($codes['skuSlug']);
            $entity->setSkuWebSlug($codes['skuWeb']);

        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,Item $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('InventoryBundle:Item')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.inventoryConfig = :inventory')
            ->setParameter('inventory', $entity->getInventoryConfig());
            $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }

    /**
     * @param @entity
     */

    public function getItemCodes(Item $entity){

        /*if(!is_object($entity->getColor())) {
            Debug::dump($entity);
            exit;
        }if(!is_object($entity->getSize())) {
            Debug::dump($entity);
            exit;
        }*/

        $masterItem         = $entity->getMasterItem()->getSTRPadCode();
        $masterSlug         = $entity->getMasterItem()->getSlug();
        $masterName         = $entity->getMasterItem()->getName();


        $color ='';
        $colorName ='';

        if(!empty($entity->getInventoryConfig()->getIsColor()) and $entity->getInventoryConfig()->getIsColor() == 1 ){
            $color              = '-C'.$entity->getColor()->getSTRPadCode();
            $colorSlug          = $entity->getColor()->getSlug();
            $colorName          = '-'.$entity->getColor()->getName();
        }elseif(!empty($entity->getColor())){
            $colorSlug          =$entity->getColor()->getSlug();
        }else{
            $colorSlug ='';
        }

        $size ='';
        $sizeName = '';

        if(!empty($entity->getInventoryConfig()->getIsSize()) and $entity->getInventoryConfig()->getIsSize() == 1){
            $size               = '-S'.$entity->getSize()->getSTRPadCode();
            $sizeSlug           = $entity->getSize()->getSlug();
            $sizeName           = '-'.$entity->getSize()->getName();
        }elseif(!empty($entity->getSize())){
            $sizeSlug           = $entity->getSize()->getSlug();
        }else{
            $sizeSlug = '';
        }

        $brand ='';
        $brandName = '';
        if(!empty($entity->getInventoryConfig()->getIsBrand()) and $entity->getInventoryConfig()->getIsBrand() == 1){
            $brand               = '-B'.$entity->getBrand()->getSTRPadCode();
            $brandSlug           = $entity->getBrand()->getSlug();
            $brandName           = '-'.$entity->getBrand()->getName();
        }elseif(!empty($entity->getBrand())){
            $brandSlug           = $entity->getBrand()->getSlug();
        }else{
            $brandSlug = '';
        }

        $model ='';
        if(!empty($entity->getInventoryConfig()->isModel()) and $entity->getInventoryConfig()->isModel() == 1){
            $model               = ' '.$entity->getModel();
            $modelSlug           = '-'.$entity->getModel();
        }else{
            $model = '';
            $modelSlug = '';
        }


        $vendor ='';
        $vendorName ='';

        if(!empty($entity->getInventoryConfig()->getIsVendor()) and $entity->getInventoryConfig()->getIsVendor() == 1 ){

            $vendor             = '-V'.$entity->getVendor()->getSTRPadCode();
            $vendorSlug         =  $entity->getVendor()->getSlug();
            $vendorName         = '-'.$entity->getVendor()->getVendorCode();

        }elseif(!empty($entity->getVendor())){
            $vendorSlug           = $entity->getVendor()->getSlug();
        }else{
            $vendorSlug = '';
        }
        $categorySlug = "";
        $categoryName = '';
        if($entity->getCategory() and !empty($entity->getInventoryConfig()->isCategory()) and $entity->getInventoryConfig()->isCategory() == 1 ){
            $category             = '';
            $categorySlug         = $entity->getCategory()->getSlug();
            $categoryName         = $entity->getCategory()->getName();

        }elseif(!empty($entity->getCategory())){
            $categorySlug           = $entity->getCategory()->getSlug();
        }else{
            $categorySlug = '';
        }

        if($entity->getCategory() and $entity->getInventoryConfig()->isCategory() == 1){
            $sku            = $masterItem.$color.$size.$brand.$vendor.$model;
            $name           = $categoryName.' '.$masterName.$colorName.$sizeName.$brandName.$vendorName.$model;
            $skuSlug        = $categorySlug.$masterSlug.$colorSlug.$sizeSlug.$brandSlug.$vendorSlug.$modelSlug;

        }else{

            $sku            = $masterItem.$color.$size.$brand.$vendor.$model;
            $name           = $masterName.$colorName.$sizeName.$brandName.$vendorName.$model;
            $skuSlug        = $masterSlug.$colorSlug.$sizeSlug.$brandSlug.$vendorSlug.$modelSlug;

        }
        $domainSlug     = $entity->getInventoryConfig()->getGlobalOption()->getSlug();
        $skuWeb         = $skuSlug.'_'.$domainSlug;
        $data = array('name'=> $name,'sku'=> $sku,'skuSlug'=> $skuSlug,'skuWeb'=> $skuWeb);
        return $data;
    }

    private  function getStrPad($lastCode,$limit)
    {
        $data = str_pad($lastCode,$limit, '0', STR_PAD_LEFT);
        return $data;
    }
}