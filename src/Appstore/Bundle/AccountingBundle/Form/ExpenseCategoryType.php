<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Appstore\Bundle\AccountingBundle\Repository\AccountHeadRepository;
use Appstore\Bundle\AccountingBundle\Repository\ExpenseCategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExpenseCategoryType extends AbstractType
{

    /** @var  ExpenseCategoryRepository */
    private $em;

    /** @var  AccountHeadRepository */
    private $accountHead;

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(ExpenseCategoryRepository $em , AccountHeadRepository $accountHead, GlobalOption $globalOption)
    {
        $this->em = $em;
        $this->globalOption = $globalOption;
        $this->accountHead = $accountHead;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter expense category name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('accountHead', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountHead',
                'empty_value' => '---Choose a account head---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2 m-wrap'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'choices'=> $this->ExpenseAccountChoiceList()
            ))

            ->add('parent', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent expense category---',
                'attr'=>array('class'=>'ExpenseCategory m-wrap span12'),
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory',
                'property' => 'nestedLabel',
                'choices'=> $this->ExpenseCategoryChoiceList()
            ))
            ->add('status')


        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product_bundle_accountingbundle_expensecategory';
    }

    /**
     * @return mixed
     */
    protected function ExpenseCategoryChoiceList()
    {
        return $this->em->getFlatExpenseCategoryTree($this->globalOption);

    }

    /**
     * @return mixed
     */
    protected function ExpenseAccountChoiceList()
    {
        return $this->accountHead->getAccountHeadTrees();

    }


}
