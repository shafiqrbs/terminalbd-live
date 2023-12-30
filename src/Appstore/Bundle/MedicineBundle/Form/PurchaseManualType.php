<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseManualType extends AbstractType
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
            ->add('memo','text', array('attr'=>array('class'=>'m-wrap span12 inputs ','required' => false ,'label' => 'form.name','placeholder'=>'Memo no','autocomplete'=>'off')))
            ->add('receiveDate','date', array('attr'=>array('class'=>'m-wrap span12 inputs','placeholder'=>'Enter receive date')))
            ->add('payment','text', array('attr'=>array('class'=>'numeric span12 inputs m-wrap remove-value','placeholder'=>'Payment amount')));
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
