<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentSalaryType extends AbstractType
{

    public  $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('paidAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Pay amount')))
            ->add('adjustmentAmount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Adjustment amount')))
            ->add('totalAmount','hidden')
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Remark')))
            ->add('salaryMonth', 'choice', array(
                'attr'=>array('class'=>'span8 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please choose required'))
                ),
                'choices' => array(
                    'January'       => 'January',
                    'February'      => 'February',
                    'March'         => 'March',
                    'April'         => 'April',
                    'May'           => 'May',
                    'June'          => 'June',
                    'July'          => 'July',
                    'August'        => 'August',
                    'September'     => 'September',
                    'October'       => 'October',
                    'November'      => 'November',
                    'December'      => 'December',
                ),
            ))
            ->add('salaryYear', 'choice', array(
                'attr'=>array('class'=>'span4 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please choose required'))
                ),
                'choices' => array(
                    '2016'       => '2016',
                    '2017'      => '2017',
                    '2018'      => '2018',
                    '2019'      => '2019',
                    '2020'      => '2020',
                    '2021'      => '2021',
                    '2022'      => '2022',
                    '2023'      => '2023',
                    '2024'      => '2024',
                    '2025'      => '2025',

                ),
            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'empty_value' => '---Choose a transaction method---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2 transactionMethod'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.name");
                }
            ))
            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->user->getGlobalOption()->getId());
                },
            ))

            ->add('accountMobileBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'empty_value' => '---Choose a mobile banking---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->user->getGlobalOption()->getId());
                },
            ))
            ->add('salarySetting', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\SalarySetting',
                'empty_value' => '---Select invoice---',
                'property' => 'invoice',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status  =1")
                        ->andWhere("e.process = 'approved'")
                        ->andWhere("e.process != 'done' ")
                        ->andWhere("e.user  =".$this->user->getId());
                },
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\PaymentSalary'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_accountingBundle_paymentsalary';
    }
}
