<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseItemManualType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stockName','text', array('attr'=>array('class'=>'m-wrap span12 select2StockMedicinePurchase input','placeholder'=>'Enter stock medicine name')))
            ->add('expirationEndDate','text', array('attr'=>array('class'=>'m-wrap span12 dateCalendar input','placeholder'=>'Expiry date','autoComplete'=>'off')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap purchase-input span12 input','placeholder'=>'Total MRP','autoComplete'=>'off')))
            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap purchase-input span12 input','placeholder'=>'Total Purchase','autoComplete'=>'off')))
            ->add('bonusQuantity','text', array('attr'=>array('class'=>'m-wrap span9','placeholder'=>'Bonus QTY','autoComplete'=>'off')))
            ->add('itemPercent','number', array('attr'=>array('class'=>'m-wrap span8 numeric','placeholder'=>'Item Percent','autoComplete'=>'off')))
            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap purchase-input span8 form-control input-number input red-border','placeholder'=>'Quantity','autoComplete'=>'off')));
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
    public function discountPercentList()
    {
        $array = array(

            1=>1,
            2=>2,
            3=>3,
            4=>4,
            5=>5,
            6=>6,
            7=>7,
            8=>8,
            9=>9,
            10=>10,
            11=>11,
            12=>12,
            13=>13,
            14=>14,
            15=>15,
            16=>16,
            17=>17,
            18=>18,
            19=>19,
            20=>20,
            21=>21,
            22=>22,
            23=>23,
            24=>24,
            25=>25,
            26=>26,
            27=>27,
            28=>28,
            29=>29,
            30=>30,
            31=>31,
            32=>32,
            33=>33,
            34=>34,
            35=>35,
            36=>36,
            37=>37,
            38=>38,
            39=>39,
            40=>40,
            41=>41,
            42=>42,
            43=>43,
            44=>44,
            45=>45,
            46=>46,
            47=>47,
            48=>48,
            49=>49,
            50=>50
        );
        return $array;


    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'purchaseItem';
    }
}
