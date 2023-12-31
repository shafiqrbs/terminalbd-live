<?php

namespace  Appstore\Bundle\AccountingBundle\Repository;

use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory;
use Core\UserBundle\Entity\User;
use Doctrine\Common\Util\Debug;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * ExpenseCategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExpenseCategoryRepository extends MaterializedPathRepository
{



    public function build_child($oldID,$array)
    {

        $em = $this->_em;
        global $exclude;
        //echo $menu_id;
        $tempTree="";

        $childData = $em->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('parent'=>$oldID),array('name'=>'asc'));

        foreach ( $childData as $child ){

            if ( $child->getId() != $child->getParent()->getId() )
            {
                if(in_array($child->getId(), $array)){
                    $tempTree .= '<option value="'.$child->getId().'" rel="'.$this->depth.'">';
                    for ( $c=0;$c<$this->depth;$c++ )
                    { $tempTree .= "&nbsp;&nbsp;&nbsp;"; }
                    for ( $c=0; $c < $this->depth; $c++ )
                    { $tempTree .= ">"; }
                    $tempTree .= "" . $child->getName(). "</option>";
                }

                $this->depth++;
                $tempTree .=$this->build_child($child->getId(),$array);
                $this->depth--;
                if(is_array($exclude)){
                    array_push($exclude,$child->getId());
                }
            }
        }

        return $tempTree;
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

    public function getFlatExpenseCategoryTree($globalOption)
    {

        $categories = $this->createQueryBuilder("node")
            ->where('node.globalOption = :option')
            ->andWhere('node.level = :level')
            ->setParameter('option', $globalOption)
            ->setParameter('level', 1)
            ->orderBy('node.level','ASC')
            ->getQuery()->getResult();

        $arr =array();
        $array =array();
        if(!empty($categories)){
            /* @var $category ExpenseCategory */
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

    private function buildFlatTree($categories, &$array = array())
    {
        usort($categories, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($categories as $ExpenseCategory) {
            $array[$ExpenseCategory['id']] = $this->formatLabel($ExpenseCategory['level'], $ExpenseCategory['name']);
            if(isset($ExpenseCategory['__children'])) {
                $this->buildFlatTree($ExpenseCategory['__children'], $array);
            }
        }
    }

    private function buildFlatExpenseCategoryTree($categories, &$array = array())
    {
        usort($categories, function($a, $b){
            return strcmp($a["name"], $b["name"]);
        });

        foreach($categories as $ExpenseCategory) {
            $array[] = $this->find($ExpenseCategory['id']);
            if(isset($ExpenseCategory['__children'])) {
                $this->buildFlatExpenseCategoryTree($ExpenseCategory['__children'], $array);
            }
        }
    }

    private function formatLabel($level, $value) {

        $level = $level - 1;
        return str_repeat("-", $level * 3) . str_repeat(">", $level) . "$value";

    }

    public function getCategoryOptions($globalOption){

        $ret = array();
        $em = $this->_em;
        $categories = $em->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('globalOption' => $globalOption ,'status' => 1),array('name'=>'asc'));

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

        //\Doctrine\Common\Util\Debug::dump($ret);
        //exit;
    }

    public function reportHmsExpenditure(User $user,$data)
    {

    }

    public function getApiCategory(GlobalOption $option)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $option->getId());
        $qb->orderBy('e.name','ASC');
        $result = $qb->getQuery()->getResult();

        $data = array();

        /* @var $row ExpenseCategory */

        foreach($result as $key => $row) {

            $data[$key]['global_id']            = (int) $option->getId();
            $data[$key]['category_id']          = (int) $row->getId();
            $data[$key]['name']                 = $row->getName();
            $data[$key]['slug']                 = $row->getSlug();

        }

        return $data;

    }

    public function generateCommission(GlobalOption $option, AccountHead $head)
    {
        $em = $this->_em;
        $find = $this->findOneBy(array('globalOption'=>$option,'accountHead'=>$head,'name'=>'Commission'));
        if(empty($find)){
            $entity = new ExpenseCategory();
            $entity->setGlobalOption($option);
            $entity->setAccountHead($head);
            $entity->setName('Commission');
            $em->persist($entity);
            $em->flush();
            return $entity;
        }
        return $find;
    }


}
