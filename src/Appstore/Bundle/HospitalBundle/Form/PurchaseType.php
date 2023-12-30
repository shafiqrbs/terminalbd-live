<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseType extends AbstractType
{
    /** @var  HospitalConfig */

    public  $option;

    public function __construct(GlobalOption $option)
    {
        $this->option = $option;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('medicineVendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineVendor',
                'empty_value' => '---Choose a vendor/supplier ---',
                'property' => 'companyName',
                'attr'=>array('class'=>'m-wrap span12 medicineVendor select2 inputs'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.medicineConfig =".$this->option->getMedicineConfig()->getId())
                        ->orderBy('e.companyName','ASC');
                },
            ))

            ->add('medicinePurchaseReturn', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturn',
                'empty_value' => '--Adjustment return--',
                'property' => 'invoiceAmount',
                'attr'=>array('class'=>'m-wrap span12 inputs'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('wt')
                        ->where("wt.medicineConfig =".$this->option->getMedicineConfig()->getId())
                        ->andWhere("wt.process ='approved'")
                        ->andWhere("wt.adjusted != 1");
                },
            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'empty_value' => '---Choose a Transaction---',
                'attr'=>array('class'=>'m-wrap span12 inputs transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->option->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('accountMobileBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'empty_value' => '---Choose a mobile banking---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->option->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))
            ->add('memo','text', array('attr'=>array('class'=>'m-wrap span12 inputs ','required' => false ,'label' => 'form.name','placeholder'=>'Memo no')))
          //  ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12  resize ','rows'=>3,'required' => true ,'label' => 'form.name','placeholder'=>'Enter remark')))
            ->add('receiveDate','date', array('attr'=>array('class'=>'m-wrap span12 inputs','placeholder'=>'Enter receive date')))
            ->add('payment','text', array('attr'=>array('class'=>'numeric span12 inputs m-wrap remove-value','placeholder'=>'Payment amount')))
            ->add('discountCalculation','number', array('attr'=>array('class'=>'m-wrap span12 salesInput','placeholder'=>'Add payment discount','data-original-title'=>'Add payment discount','autocomplete'=>'off')))
            ->add('invoiceMode', 'choice', array(
                'attr'=>array('class'=>'m-wrap invoice-mode span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'invoice' => 'Vendor Invoice',
                    'manual' => 'Manual',
                ),
            ))
            ->add('discountType', 'choice', array(
                'attr'=>array('class'=>'m-wrap discount-type span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'percentage' => 'Percentage',
                    'flat' => 'Flat',
                ),
            ))
           // ->add('asInvestment')
            ->add('discount','hidden');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'medicinepurchase';
    }
}
