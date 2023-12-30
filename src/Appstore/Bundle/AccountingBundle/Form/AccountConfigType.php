<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountConfigType extends AbstractType
{

    /* @var $global GlobalOption */

    public  $global;

    public function __construct(GlobalOption $global)
    {
        $this->global = $global;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('capitalInvestor', 'entity', [
                'class'     => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'property'  => 'name',
                'empty_value' => '---Choose a capital investor---',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'choice_translation_domain' => true,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.parent",'c')
                        ->where("e.status = 1")
                        ->andWhere("c.slug ='capital-investment'")
                        ->andWhere("e.globalOption =".$this->global->getId())
                        ->orderBy("e.name", "ASC");
                }
            ])
            ->add('accountClose')
            ->add('salesReceiveSms')
            ->add('purchase')
            ->add('customPrint')
            ->add('borderColor','text', array('attr'=>array(
                'class'=>'m-wrap span9 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('bodyFontSize', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array('' => 'Font Size','10px' => '10px',  '11px' => '11px','12px' => '12px','13px' => '13px','14px' => '14px', '15px' => '15px', '16px' => '16px', '17px' => '17px','18px' => '18px',  '20px' => '20px', '22px' => '22px','24px' => '24px', '26px' => '26px',  '28px' => '28px','30px' => '39px','32px' => '32px','34px' => '34px','36px' => '36px', '38px' => '38px', '40px' => '40px','42px' => '42px',  '44px' => '44px', '46px' => '46px','48px' => '48px'),
            ))
            ->add('businessMode', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array('Proprietorship' => 'Proprietorship',  'Partnership' => 'Partnership'),
            ))
            ->add('borderWidth','text',array('attr'=>array('class'=>'m-wrap numeric span8','maxLength'=>2)))
            ->add('address','textarea',array('attr'=>array('class'=>'m-wrap span12','row'=>5)))
            ->add('isPowered')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('invoiceWidth','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('invoiceHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
           ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountingConfig'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'config';
    }

}
