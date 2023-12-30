<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForDmsType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerForDpsType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Appstore\Bundle\DomainUserBundle\Form\PatientForPrescriptionType;
use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceCustomerType extends AbstractType
{

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption)
    {
        $this->globalOption     = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('assignDoctor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular',
                'property' => 'name',
                'multiple'    => false,
                'expanded' => false,
                'attr'=>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.mode = 'doctor'")
                        ->andWhere('e.dpsConfig =:dpsConfig')
                        ->setParameter('dpsConfig', $this->globalOption->getDpsConfig()->getId())
                        ->orderBy("e.name","ASC");
                }
            ));
           $builder->add('customer', new PatientForPrescriptionType());
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dpsinvoice';
    }

}
