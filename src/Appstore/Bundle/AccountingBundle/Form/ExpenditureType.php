<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\AccountingBundle\Repository\ExpenseCategoryRepository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExpenditureType extends AbstractType
{

    /** @var  GlobalOption */

    public  $globalOption;

    /** @var  ExpenseCategoryRepository */

    private $expenseCategoryRepository;

    public function __construct(GlobalOption $globalOption, ExpenseCategoryRepository $expenseCategoryRepository )
    {
        $this->globalOption = $globalOption;
        $this->expenseCategoryRepository = $expenseCategoryRepository;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add payment amount BDT'))
                )))
            ->add('remark','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Remark...')))
            ->add('toUser', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'empty_value' => '---Choose a account user---',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array(new NotBlank(array('message'=>'Please select to user name'))),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->where("u.isDelete != 1")
                        ->andWhere("u.domainOwner = 2")
                        ->andWhere("u.globalOption =".$this->globalOption->getId())
                        ->orderBy("u.username", "ASC");
                },
            ))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'empty_value' => '---Choose a transaction method---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap transactionMethod'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.slug != 'cash-on-delivery'")
                        ->orderBy("e.id");
                }
            ))
            ->add('accountBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'empty_value' => '---Choose a bank---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->globalOption->getId());
                },
            ))

            ->add('accountMobileBank', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'empty_value' => '---Choose a mobile account---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption =".$this->globalOption->getId());
                },
            ))

            ->add('expenseCategory', 'entity', array(
                'required'    => true,
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select category'))
                ),
                'empty_value' => '---Select expense category---',
                'attr'=>array('class'=>'ExpenseCategory m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->ExpenseCategoryChoiceList()
            ))

            /*->add('toUser', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'empty_value' => '---Choose a user---',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array(new NotBlank(array('message'=>'Please select to user name'))),
                'query_builder' => function(EntityRepository $er){
	                return $er->createQueryBuilder('u')
	                          ->where("u.isDelete != 1")
	                          ->andWhere("u.enabled = 1")
	                          ->andWhere("u.domainOwner = 2")
	                          ->andWhere("u.globalOption =".$this->globalOption->getId())
	                          ->orderBy("u.username", "ASC");
                },
            ))*/
            ->add('file');
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\Expenditure'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'expenditure';
    }

    /**
     * @return mixed
     */
    protected function ExpenseCategoryChoiceList()
    {
        return $ExpenseCategoryTree = $this->expenseCategoryRepository->getFlatExpenseCategoryTree($this->globalOption);

    }



}
