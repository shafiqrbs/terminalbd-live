<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\DomainUserBundle\Repository\CustomerRepository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Repository\MedicineMinimumStockRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedicineMinimumStockType extends AbstractType
{

    /** @var $em MedicineMinimumStockRepository */
    private $em;

    /** @var $config MedicineConfig */
    private $config;



    function __construct(MedicineMinimumStockRepository $em, MedicineConfig $config)
    {
        $this->em = $em;
        $this->config = $config->getId();

    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('minQuantity','text', array('attr'=>array('class'=>'m-wrap span12 inputs ','required' => true ,'label' => 'form.name','placeholder'=>'Enter min qnt'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter min qnt'))
                )))

            ->add('reorderQuantity','text', array('attr'=>array('class'=>'m-wrap span12 inputs ','required' => true ,'label' => 'form.name','placeholder'=>'Enter min qnt'),
                ))

            ->add('company', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select company name'))),
                'empty_value' => '---Choose a company---',
                'choices' => $this->companyChoiceList($this->config),
            ))
            ->add('medicineForm', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select Medicine Form'))),
                'empty_value' => '---Choose a Medicine Form---',
                'choices' => $this->medicineFormList(),
            ));
    }

    public function companyChoiceList($config)
    {
        return $syndicateTree = $this->em->companyList($config);

    }
    
     public function medicineFormList()
    {
        return $syndicateTree = $this->em->medicineFormList();

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineMinimumStock'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'medicineMinimumStock';
    }
}
