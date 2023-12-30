<?php

namespace Setting\Bundle\ToolBundle\Importer;
use Appstore\Bundle\MedicineBundle\Entity\MedicineBrand;
use Appstore\Bundle\MedicineBundle\Entity\MedicineCompany;
use Appstore\Bundle\MedicineBundle\Entity\MedicineGeneric;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class Excel
{
    use ContainerAwareTrait;

    private $data = array();
    private $cache = array();

	public function isValid($data) {

		return true;

	}

    public function import($data)
    {
        $this->data = $data;

        foreach($this->data as $key => $item) {

            $name = $item['BrandName'];
            $strength  = $item['Strength'];
            $dosage = $item['Dosage'];
            $name = $this->getDoctrain()->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('name' => $name,'strength' => $strength,'medicineForm' => $dosage));
            if(empty($name) and !empty($item['BrandName']) ) {
                $medicine = new MedicineBrand();
                $medicine->setMedicineCompany( $this->getMedicineCompany( $item ) );
                $medicine->setMedicineGeneric( $this->getMedicineGeneric( $item ) );
                $medicine->setName( $item['BrandName'] );
                $medicine->setStrength( $item['Strength'] );
                $medicine->setMedicineForm( $item['Dosage'] );
                $medicine->setUseFor( $item['UseFor'] );
                $medicine->setDar( $item['DAR'] );
                $this->persist( $medicine );
                $this->flush();
            }

        }
    }

    public function importOld($data)
    {
        $this->data = $data;

        foreach($this->data as $key => $item) {

          	$name = $item['BrandName'];
	        $strength  = $item['Strength'];
	        $dosage = $item['Dosage'];
            $name = $this->getDoctrain()->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('name' => $name,'strength' => $strength,'medicineForm' => $dosage));
	        if(empty($name) and !empty($item['BrandName']) ) {
		        $medicine = new MedicineBrand();
		        $medicine->setMedicineCompany( $this->getMedicineCompany( $item ) );
		        $medicine->setMedicineGeneric( $this->getMedicineGeneric( $item ) );
		        $medicine->setName( $item['BrandName'] );
		        $medicine->setStrength( $item['Strength'] );
		        $medicine->setMedicineForm( $item['Dosage'] );
		        $medicine->setUseFor( $item['UseFor'] );
		        $medicine->setDar( $item['DAR'] );
		        $this->persist( $medicine );
		        $this->flush();
	        }

        }
    }


	private function getMedicineCompany($item)
	{
		$company = $this->getCachedData('Company', $item['Company']);
		$companyRepository = $this->getMedicineCompanyRepository();
		if($company == NULL) {
			$company = $companyRepository->findOneBy(array(
				'name'                => $item['Company']
			));
			if($company == NULL) {
				$company = new MedicineCompany();
				$company->setName($item['Company']);
				$company = $this->save($company);
			}
			$this->setCachedData('Company', $item['Company'], $company);
		}

		return $company;
	}

	private function getMedicineGeneric($item)
	{
		$generic = $this->getCachedData('GenericName', $item['GenericName']);
		$genericRepository = $this->getMedicineGenericRepository();
		if($generic == NULL) {

			$generic = $genericRepository->findOneBy(array(
				'name'                => $item['GenericName']
			));
			if($generic == null) {
				$generic = new MedicineGeneric();
				$generic->setName($item['GenericName']);
				$generic = $this->save($generic);
			}
			$this->setCachedData('GenericName', $item['GenericName'], $generic);
		}

		return $generic;
	}

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getEntityManager()
    {
        return $this->getDoctrain()->getManager();
    }

    /**
     * @return \Appstore\Bundle\MedicineBundle\Repository\MedicineCompanyRepository
     */
    private function getMedicineCompanyRepository()
    {
        return $this->getDoctrain()->getRepository('MedicineBundle:MedicineCompany');
    }

    /**
     * @return \Appstore\Bundle\MedicineBundle\Repository\MedicineGenericRepository
     */
    private function getMedicineGenericRepository()
    {
        return $this->getDoctrain()->getRepository('MedicineBundle:MedicineGeneric');
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