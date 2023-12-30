<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\CustomerForHospitalType;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    /** @var  HmsCategoryRepository */
    private $emCategory;

    /** @var  GlobalOption */
    private $globalOption;

    /** @var  HospitalConfig */
    private $config;



    function __construct(GlobalOption $globalOption , HmsCategoryRepository $emCategory ,  LocationRepository $location)
    {
        $this->location         = $location;
        $this->emCategory       = $emCategory;
        $this->globalOption     = $globalOption;
        $this->config     = $globalOption->getHospitalConfig();
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('cardNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment card no','data-original-title'=>'Add payment card no','autocomplete'=>'off')))
            ->add('transactionId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment transaction id','data-original-title'=>'Add payment transaction id','autocomplete'=>'off')))
            ->add('paymentMobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add payment mobile no','data-original-title'=>'Add payment mobile no','autocomplete'=>'off')))
            ->add('payment','number', array('attr'=>array('class'=>'tooltips payment numeric span11 input2 m-wrap','data-trigger' => 'hover','placeholder'=>'Receive','data-original-title'=>'Enter valid receive amount, if receive amount is due input zero','autocomplete'=>'off'),
            ))
            ->add('discountCalculation','number', array('attr'=>array('class'=>'tooltips initialDiscount span11 input2 m-wrap','data-trigger' => 'hover','placeholder'=>'Discount','data-original-title'=>'Enter valid discount amount','autocomplete'=>'off'),
            ))
            ->add('consultant','text', array('attr'=>array('class'=>'consultant select2 span12 m-wrap','placeholder'=>'Search consultant name','autocomplete'=>'off'),
            'required'=> false,'mapped'=> false
            ))

            ->add('referredId','text', array('attr'=>array('class'=>'referred select2 span12 m-wrap','placeholder'=>'Search referred name','autocomplete'=>'off'),
            'required'=> false,'mapped'=> false
            ))

            ->add('isHold',CheckboxType::class, array(
                'attr'=>array('class'=>'tooltips custom-control-input','data-trigger' => 'hover','placeholder'=>'Receive')
                ,'required'=> false,'mapped'=> false
            ))
            ->add('isDiscount',CheckboxType::class, array(
                'attr'=>array('class'=>'tooltips custom-control-input isDiscount','data-trigger' => 'hover','placeholder'=>'Receive')
                ,'required'=> false,'mapped'=> false
            ))
            ->add('discount','hidden',array('attr'=>array('class'=>'discount')))
            ->add('isDiscount',CheckboxType::class, array(
                'attr'=>array('class'=>'tooltips custom-control-input','data-trigger' => 'hover','placeholder'=>'Receive')
            ,'required'=> false,'mapped'=> false
            ))
            ->add('discountRequestedBy','text', array('attr'=>array('class'=>'m-wrap span12 requested2User','placeholder'=>'Discount requested by','autocomplete'=>'off')))
            ->add('discountRequestedComment','textarea', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Discount requested comment','autocomplete'=>'off')))
            ->add('comment','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add remarks','autocomplete'=>'off')))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id","ASC");
                }
            ))
            ->add('paymentCard', 'entity', array(
                'required'    => false,
                'property' => 'name',
                'class' => 'Setting\Bundle\ToolBundle\Entity\PaymentCard',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose payment card---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('accountBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose receive bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))
            ->add('accountMobileBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose receive mobile bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))

        ;
        if( $this->config->isMarketingExecutive() == 1){

            $builder->add('marketingExecutive', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'marketingExecutiveEmployee',
                'attr'=>array('class'=>'span12 select2 m-wrap marketingExecutive'),
                'empty_value' => '--- Choose Marketing Executive ---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere('b.service IN(:service)') ->setParameter('service',array_values(array(14)))
                        ->andWhere("b.hospitalConfig =".$this->globalOption->getHospitalConfig()->getId())
                        ->orderBy("b.name", "ASC");
                }
            ));
        }
        if( $this->globalOption->getHospitalConfig()->isAdvanceSearchParticular() == 1 ){
            $builder->add('particulars','text', array(
                'attr'=>array('class'=>'particulars select2 span12 m-wrap','placeholder'=>'Test Name, Accessories, Surgery etc.','autocomplete'=>'off')
            ,'required'=> false,'mapped'=> false
            ));
        }
        $builder->add('referredDoctor', new InvoiceReferredDoctorType(),array('mapped'=> false));
        $builder->add('assignDoctor', new InvoiceDoctorType(),array('mapped'=> false));
        $builder->add('customer', new CustomerForHospitalType());

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\Invoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_invoice';
    }

}
