<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class JournalVoucherType extends AbstractType
{



    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('voucherNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Voucher no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter voucher no'))
                )))
            ->add('voucherType', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\VoucherType',
                'empty_value' => '---Choose a voucher type---',
                'constraints' =>array(new NotBlank(array('message'=>'Please select to voucher type'))),
                'property' => 'voucherName',
                'attr'=>array('class'=>'span12 m-wrap voucherType select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id");
                }
            )) ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountJournal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'voucher';
    }


}
