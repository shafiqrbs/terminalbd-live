<?php

namespace Appstore\Bundle\MedicineBundle\Form;

use Appstore\Bundle\InventoryBundle\Repository\SalesItemRepository;
use Appstore\Bundle\MedicineBundle\Repository\MedicineSalesItemRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SalesTemporaryItemType extends AbstractType
{


    /** @var  $globalOption GlobalOption */
    private $globalOption;

    /** @var $em MedicineSalesItemRepository */
    private $em;



    function __construct(GlobalOption $globalOption, MedicineSalesItemRepository $em)
    {
        $this->glodbalOption     = $globalOption;
        $this->em     = $em;
        $this->config     = $globalOption->getMedicineConfig();
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('stockName','text', array('attr'=>array('class'=>'m-wrap span12 select2StockMedicine input','placeholder'=>'Enter stock medicine name')))
            ->add('itemPercent','text', array('attr'=>array('class'=>'m-wrap span3 input number','autocomplete'=>'off','placeholder'=>'Percent (%)')))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span3 input number','autocomplete'=>'off','placeholder'=>'MRP')))
            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span3 form-control input-number input number','autocomplete'=>'off','placeholder'=>'QTY')));
            if($this->config->isPurchaseItem() == 1) {
                $builder->add('purchaseItem', 'choice', array(
                    'attr' => array('class' => 'm-wrap span12 input'),
                    'expanded' => false,
                    'multiple' => false,
                    'empty_value' => '---Purchase Item---',
                    'choices' => array(),
                ));
            }
    }

    public function discountPercentList()
    {
        return $this->em->discountPercentList();

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
        return 'salesTemporaryItem';
    }
}
