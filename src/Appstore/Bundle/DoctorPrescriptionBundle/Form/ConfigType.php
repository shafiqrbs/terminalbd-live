<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ConfigType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('customerPrefix','text', array('attr'=>array('class'=>'m-wrap span5','maxlength'=> 4,'placeholder'=>'max 4 char')))
            ->add('invoicePrefix','text', array('attr'=>array('class'=>'m-wrap span5 ','maxlength'=> 4,'placeholder'=>'max 4 char')))
            ->add('isDefaultMedicine')
            ->add('bodyFontSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            ->add('invoiceFontSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            ->add('sidebarFontSize', 'choice', array(
                'attr'=>array('class'=>' span12'),
                'choices' => array('' => 'Font Size', '10px' => '10px',  '12px' => '12px','14px' => '14px', '16px' => '16px','18px' => '18px',  '20px' => '20px'),
            ))
            ->add('customPrescription')
            ->add('invoicePrintLogo')
            ->add('isInvoiceTitle')
            ->add('isPrintHeader')
            ->add('investigation')
            ->add('isPrintFooter')
            ->add('printInstruction')
            ->add('headerLeftWidth','text',array('attr'=>array('class'=>'m-wrap span12')))
            ->add('headerRightWidth','text',array('attr'=>array('class'=>'m-wrap  span12')))
            ->add('bodyTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('bodyWidth','text',array('attr'=>array('class'=>'m-wrap  span12')))
            ->add('sidebarWidth','text',array('attr'=>array('class'=>'m-wrap  span12')))
            ->add('leftTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('invoiceHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_particular';
    }



}
