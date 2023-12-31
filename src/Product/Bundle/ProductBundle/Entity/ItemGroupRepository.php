<?php

namespace Product\Bundle\ProductBundle\Entity;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Query\Expr\Orx;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemGroupRepository extends MaterializedPathRepository
{

    
    public function findWithSearch($data){

        $name = isset($data['name'])? $data['name'] :'';
        $parent = isset($data['parent'])? $data['parent'] :'';
        $qb = $this->createQueryBuilder('ItemGroup');
        $qb->where('ItemGroup.level != :null')->setParameter('null', 'N;') ;
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("ItemGroup.name", "'%$name%'"  ));
        }
        if(!empty($parent)){
            $qb->andWhere("ItemGroup.parent = :parent");
            $qb->setParameter('parent', $parent);
        }
        $qb->orderBy('ItemGroup.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function getFlatInventoryItemGroupTree(InventoryConfig $config)
    {

        $categories = $this->createQueryBuilder("node")
            ->where('node.inventoryConfig = :config')
            ->setParameter('config', $config)
            ->orderBy('node.level','ASC')
            ->getQuery()->getResult();

        $arr =array();
        $array =array();
        if(!empty($categories)){

            /* @var $category Category */

            foreach($categories as $category){

                $arr[] = array(
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'level' => $category->getLevel(),
                    '__children' => $this->childrenHierarchy($category)
                );
            }
            $this->buildFlatCategoryTree($arr , $array);
        }
        return $array == null ? array() : $array;
    }

    private function buildFlatCategoryTree($categories, &$array = array())
    {
        usort($categories, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($categories as $category) {
            $array[] = $this->find($category['id']);
            if(isset($category['__children'])) {
                $this->buildFlatCategoryTree($category['__children'], $array);
            }
        }
    }

    public function getRootCategoriesQB() {

        $qb = $this->createQueryBuilder('c');

        return $qb
            ->where('c.status = :status')
            ->andWhere($qb->expr()->isNull('c.parent'))
            ->setParameter('status', 1)
            ->orderBy('c.name', 'ASC');
    }

    public function getRootCategories() {
        return $this->getRootCategoriesQB()->getQuery()->getResult();
    }

    public function getFeaturedRootCategories() {
        $qb = $this->getRootCategoriesQB();

        return $qb
            ->andWhere($qb->expr()->eq('c.feature', true))
            ->getQuery()
            ->getResult();
    }


    public function getFlatTree()
    {

        $categories = $this->childrenHierarchy();

        $this->buildFlatTree($categories, $array);

        return $array;
    }

    public function getFlatItemGroupTree()
    {

        $categories = $this->childrenHierarchy();

        $this->buildFlatItemGroupTree($categories, $array);

        return $array;
    }

    private function buildFlatTree($categories, &$array = array())
    {
        usort($categories, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($categories as $ItemGroup) {
            $array[$ItemGroup['id']] = $this->formatLabel($ItemGroup['level'], $ItemGroup['name']);
            if(isset($ItemGroup['__children'])) {
                $this->buildFlatTree($ItemGroup['__children'], $array);
            }
        }
    }

    private function buildFlatItemGroupTree($categories, &$array = array())
    {
        usort($categories, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($categories as $ItemGroup) {
            $array[] = $this->find($ItemGroup['id']);
            if(isset($ItemGroup['__children'])) {
                $this->buildFlatItemGroupTree($ItemGroup['__children'], $array);
            }
        }
    }

    private function formatLabel($level, $value) {
        $level = $level - 1;
        return str_repeat("-", $level * 3) . str_repeat(">", $level) . "$value";
    }


    public function getItemGroupOptions(){

        $ret = array();
        $em = $this->_em;
        $categories = $em->getRepository('ProductProductBundle:ItemGroup')->findBy(array('status'=>1),array('name'=>'asc'));

        foreach( $categories as $cat ){
            if( !$cat->getParent() ){
                continue;
            }
            $key = $cat->getParent()->getName();
            if(!array_key_exists($key, $ret) ){
                $ret[ $cat->getParent()->getName() ] = array();
            }
            $ret[ $cat->getParent()->getName() ][ $cat->getId() ] = $cat;
        }
        return $ret;
    }

    /**
     * @param $categories ItemGroup[]
     * @return array
     */
    public function buildItemGroupGroup($categories)
    {
        $result = array();

        foreach($categories as $ItemGroup) {

            $parentItemGroup = $this->getParentItemGroupByLevel($ItemGroup, 2);
            if(empty($parentItemGroup)) {
                continue;
            }

            $parentId = $parentItemGroup->getId();

            if(!isset($result[$parentId])) {
                $result[$parentId] = array(
                    'name' =>  $parentItemGroup->getName(),
                    'id' =>  $parentItemGroup->getId(),
                    '__children' =>  array(),
                );
            }

            $result[$parentId]['__children'][] = array(
                'name' => $ItemGroup->getName(),
                'id' => $ItemGroup->getId()
            );
        }

        return $result;
    }

    public function getItemGroupOptionGroup()
    {
        $results = $this->createQueryBuilder('node')
            ->orderBy('node.level, node.name', 'ASC')
            ->where('node.level > 1')
            ->getQuery()
            ->getResult();

        $categories = $this->getCategoriesIndexedById($results);
        $grouped = array();
        foreach ($categories as $ItemGroup) {
            switch($ItemGroup->getLevel()) {
                case 2: break;
                default:
                    $grouped[$categories[$ItemGroup->getParentIdByLevel(2)]->getName()][$ItemGroup->getId()] = $ItemGroup;
            }
        }
        return $grouped;
    }

    /**
     * @param ItemGroup $ItemGroup
     * @param int $level
     * @return ItemGroup
     */
    public function getParentItemGroupByLevel(ItemGroup $ItemGroup, $level = 1)
    {
        return $this->find($ItemGroup->getParentIdByLevel($level));
    }

    /**
     * @param $results
     * @return ItemGroup[]
     */
    protected function getCategoriesIndexedById($results)
    {
        $categories = array();

        foreach ($results as $ItemGroup) {
            $categories[$ItemGroup->getId()] = $ItemGroup;
        }
        return $categories;
    }

    public function searchAutoComplete($inventory,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.masterProducts','m');
        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("m.inventoryConfig = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }


    public function getChildIds($catId)
    {

        $query = $this->createQueryBuilder('e');
        $orX = $query->expr()->orX();
        $query->select('e.id as id');
        $orX->add("e.path like '%" .$catId."/%'");
        $query->where($orX);
        $result = $query->getQuery();
        $res = $result->getArrayResult();
        return $res;

    }

    public function getFeatureItemGroup($config, $limit)
    {
        $query = $this->createQueryBuilder('e');
        $query->where("e.ecommerceConfig = :config")->setParameter('config', $config);
        $query->andWhere("e.status = 1");
        $query->andWhere("e.feature = 1");
        $query->orderBy('e.name', 'ASC');
        if($limit > 0){
            $query->setMaxResults($limit);
        }
        return $query->getQuery()->getResult();

    }



}
