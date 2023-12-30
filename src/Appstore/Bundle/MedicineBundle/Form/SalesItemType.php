<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SalesItemType extends AbstractType
{


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

            ->add('stockName','text', array('attr'=>array('class'=>'m-wrap span12 select2StockMedicine input','placeholder'=>'Enter stock medicine name')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12 input number','placeholder'=>'MRP')))
            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span5 form-control input-number number input','placeholder'=>'QTY')))
            ->add('itemPercent','text', array('attr'=>array('class'=>'m-wrap span8 form-control input-number number input','placeholder'=>'Disc(%)')));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'salesitem';
    }
}
