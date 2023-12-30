<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseBarcodeItemType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('mapped'=>false,'attr'=>array('class'=>'m-wrap span12  barcodeInput','placeholder'=>'Enter stock medicine name')))
            ->add('barcode','text', array('attr'=>array('class'=>'m-wrap span12 barcode barcodeInput','placeholder'=>'Enter Item Barcode','autoComplete'=>'off')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap purchase-input span6 barcodeInput','placeholder'=>'Total MRP','autoComplete'=>'off')))
            ->add('bonusQuantity','text', array('attr'=>array('class'=>'m-wrap span9','placeholder'=>'Bonus QTY','autoComplete'=>'off')))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap barcodeInput span12 input-number red-border','placeholder'=>'Quantity','autoComplete'=>'off')));
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
        return 'barcodePurchaseItem';
    }
}
