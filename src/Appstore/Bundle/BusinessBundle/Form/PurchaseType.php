<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseType extends AbstractType
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

            ->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountVendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->option->getId());
                },
            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'empty_value' => '---Choose a Transaction---',
                'attr'=>array('class'=>'span12 transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))

            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
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
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->option->getId())
                        ->orderBy("b.name", "ASC");
                },
            ))

            ->add('discountCalculation','number', array('attr'=>array('class'=>'m-wrap span12 salesInput','placeholder'=>'Add payment discount','data-original-title'=>'Add payment discount','autocomplete'=>'off')))
            ->add('discountType', 'choice', array(
                'attr'=>array('class'=>'m-wrap discount-type span12'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'percentage' => 'Percentage',
                    'flat' => 'Flat',
                ),
            ))
            ->add('file', FileType::class,array(
                'required' => false,
                'attr'=>array('class'=>'custom-file-input'),
                'constraints' => array(new File(array( 'maxSize' => '1024k',
                    'mimeTypes' =>array('application/pdf',
                        'application/x-pdf',
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'image/gif'),
                    'mimeTypesMessage' => 'Please upload a valid document',))),
            ))
            ->add('memo','text', array('attr'=>array('class'=>'m-wrap span12','required' => true ,'label' => 'form.name','placeholder'=>'Memo no')))
            ->add('receiveDate','date', array('attr'=>array('class'=>'m-wrap span12 inputs','placeholder'=>'Enter receive date')))
            ->add('payment','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Payment amount','autocomplete'=>'off')))
            ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=> 5,'placeholder'=>'Enter narration','autocomplete'=>'off')
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'purchase';
    }
}
