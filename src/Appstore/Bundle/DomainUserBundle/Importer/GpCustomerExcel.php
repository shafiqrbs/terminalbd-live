<?php

namespace Appstore\Bundle\DomainUserBundle\Importer;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use DoctrineExtensions\Query\Sqlite\Date;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Constraints\DateTime;


class GpCustomerExcel
{
    use ContainerAwareTrait;

    protected $option;

    private $data = array();

    public function import($data)
    {
        $this->data = $data;
        foreach($this->data as $key => $item) {

            $user = "{$this->clean($item['mobile'])}";
            $option = $this->option;
            $existUser = $this->getDoctrain()->getRepository('UserBundle:User')->findOneBy(array('username' => $user,'globalOption' => $option));
            if(empty($existUser)){
                $em = $this->getDoctrain()->getManager();
                $profile = new Customer();
                $profile->setGlobalOption($option);
                $profile->setCustomerType('customer');
                $profile->setName($item['name']);
                $profile->setMobile($user);
                $em->persist($profile);
                $em->flush();
            }

        }

    }

    function clean($string) {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '', $string); // Replaces multiple hyphens with single one.
    }

    public function setGlobalOption($option)
    {
        $this->option = $option;
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