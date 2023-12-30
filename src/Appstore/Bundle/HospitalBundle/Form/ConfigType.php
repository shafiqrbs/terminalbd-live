<?php

namespace Appstore\Bundle\HospitalBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;


class ConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('vatRegNo','text', array('attr'=>array('class'=>'m-wrap span8','placeholder'=>'Enter vat registration no')))
            ->add('customerPrefix','text', array('attr'=>array('class'=>'m-wrap span12','maxlength'=> 4,'placeholder'=>'max 4 char')))
            ->add('invoicePrefix','text', array('attr'=>array('class'=>'m-wrap span12 ','maxlength'=> 4,'placeholder'=>'max 4 char')))
            ->add('vatPercentage','integer',array('attr'=>array('class'=>'m-wrap numeric span5','max'=> 100)))
            ->add('vatEnable')
            ->add('isMarketingExecutive')
            ->add('initialDiagnosticShow')
            ->add('services', 'entity', array(
                'required'    => false,
                'expanded'    => true,
                'multiple'    => true,
                'property' => 'name',
                'attr'=>array('class'=>'check-list m-wrap'),
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Service',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.status =1')
                        ->orderBy("e.name","ASC");
                }

            ))
            ->add('invoiceProcess',
                'choice', array(
                    'attr'=>array('class'=>'check-list  span8'),
                    'choices' => array(
                        'diagnostic'    => 'Diagnostic',
                        'admission'   => 'Admission',
                        'visit'  => 'Visit',
                        'commission'  => 'Commission ',
                    ),
                    'required'    => true,
                    'multiple'    => true,
                    'expanded'  => true,
                    'empty_data'  => null,
            ))
            ->add('prescriptionBuilder')
            ->add('commissionAutoApproved')
            ->add('advanceSearchParticular')
            ->add('customPrint')
            ->add('invoicePrintLogo')
            ->add('isInvoiceTitle')
            ->add('isPrintHeader')
            ->add('isPrintReportHeader')
            ->add('isInventory')
            ->add('isPrintFooter')
            ->add('headerFile')
            ->add('footerFile')
            ->add('appointmentPrescription')
            ->add('printInstruction')
            ->add('printOff')
            ->add('address','textarea',array('attr'=>array('class'=>'m-wrap span12','rows'=>5)))
            ->add('messageAdmission','textarea',array('attr'=>array('class'=>'m-wrap span12 editor','rows'=>8, 'placeholder'=>'Enter message for admission')))
            ->add('messageDiagnostic','textarea',array('attr'=>array('class'=>'m-wrap span12 editor','rows'=>8, 'placeholder'=>'Enter message for Diagonestic')))
            ->add('messageVisit','textarea',array('attr'=>array('class'=>'m-wrap span12 editor','rows'=>8, 'placeholder'=>'Enter message for visit')))
            ->add('cssContent','textarea',array('attr'=>array('class'=>'m-wrap span12','rows'=>8, 'placeholder'=>'Enter Print custom print')))
            ->add('invoiceHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printMarginReportLeft','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('reportHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printMarginReportTop','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\HospitalConfig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'config';
    }

    /**
     * @return mixed
     */
    protected function PathologyChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 2 /** Pathology */ );

    }
    /**
     * @return mixed
     */
    protected function DepartmentChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 7 /** Department */);

    }

}
