<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class StockOutType extends AbstractType
{
    /** @var  HospitalConfig */

    public  $config;

    public function __construct(HospitalConfig $config)
    {
        $this->config = $config;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

	        ->add('invoiceFor', 'choice', array(
		        'attr'=>array('class'=>'span12 select-custom'),
		        'expanded'      =>false,
		        'multiple'      =>false,
		        'choices' => array(
			        'expenditure' => 'Expenditure',
			        'customer' => 'Customer',
		        ),
	        ))
	        ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12 resize ','rows'=>3,'required' => false ,'label' => 'form.name','placeholder'=>'Enter remark')))
            ->add('payment','text', array('attr'=>array('class'=>'numeric m-wrap span12','placeholder'=>'Payment amount')
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\HmsStockOut'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'stockout';
    }
}
