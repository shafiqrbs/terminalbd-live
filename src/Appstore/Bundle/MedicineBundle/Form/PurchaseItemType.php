<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseItemType extends AbstractType
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
            ->add('stockName','text', array('attr'=>array('class'=>'m-wrap span12 select2StockMedicinePurchaseItem input','placeholder'=>'Enter stock medicine name')))
            ->add('expirationEndDate','text', array('attr'=>array('class'=>'m-wrap span12 dateCalendar input','placeholder'=>'Expiry date','autoComplete'=>'off')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap purchase-input span10 input','placeholder'=>'Total MRP','autoComplete'=>'off')))
            ->add('bonusQuantity','text', array('attr'=>array('class'=>'m-wrap span9','placeholder'=>'Bonus QTY','autoComplete'=>'off')))
          //  ->add('batchno','text', array('attr'=>array('class'=>'m-wrap purchase-input span12 form-control input-number input','placeholder'=>'Batch no','autoComplete'=>'off')))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap purchase-input span12 form-control input-number input red-border','placeholder'=>'Quantity','autoComplete'=>'off')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'purchaseItem';
    }
}
