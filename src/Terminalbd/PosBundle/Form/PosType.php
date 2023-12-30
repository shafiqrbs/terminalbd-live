<?php

namespace Terminalbd\PosBundle\Form;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PosType extends AbstractType
{

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption )
    {
        $this->globalOption  = $globalOption;
        $this->config  = $globalOption->getInventoryConfig()->getId();
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('payment','text', array('attr'=>array('class'=>'m-wrap span4 input-number invoice-change payment amount tooltips','data-trigger' => 'hover','placeholder'=>'Amount','data-original-title'=>'Add receive amount','autocomplete'=>'off')))
            ->add('deliveryCharge','text', array('attr'=>array('class'=>'tooltips span12 m-wrap input-number amount invoice-change','data-trigger' => 'hover','placeholder'=>'Receive BDT','data-original-title'=>'Delivery Charge','autocomplete'=>'off')))
            ->add('cardNo','text', array('attr'=>array('class'=>'m-wrap span12 invoice-change','placeholder'=>'Card no','data-original-title'=>'Add payment card no','autocomplete'=>'off')))
            ->add('transactionId','text', array('attr'=>array('class'=>'m-wrap span12 invoice-change','placeholder'=>'Payment ID','data-original-title'=>'Add payment transaction id','autocomplete'=>'off')))
            ->add('paymentMobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile invoice-change','placeholder'=>'Payment Mobile','data-original-title'=>'Add payment mobile no','autocomplete'=>'off')))

            ->add('discount','hidden',array('attr'=>array('class'=>'discount amount')))
            ->add('discountType', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap invoice-change'),
                'choices'   => array('flat' => 'Flat', 'percent' => 'Percent'),
                'required'  => true,
            ))
            ->add('isHold','checkbox', array('attr'=>array('class'=>'m-wrap isHold tooltips','data-trigger' => 'hover','placeholder'=>'Hold','data-original-title'=>'Invoice Hold','autocomplete'=>'off')))
            ->add('discountCalculation','text', array('attr'=>array('class'=>'tooltips span12 m-wrap input-number amount invoice-change ','data-trigger' => 'hover','placeholder'=>'Discount','data-original-title'=>'Enter discount amount','autocomplete'=>'off')))
            ->add('discountCoupon','text', array('mapped'=> false,'attr'=>array('class'=>'tooltips span12 m-wrap input-number discountCoupon invoice-change','data-trigger' => 'hover','placeholder'=>'Coupon No','data-original-title'=>'Enter Discount Coupon No','autocomplete'=>'off')))
            ->add('paymentCard', 'entity', array(
                'required'    => false,
                'property' => 'name',
                'class' => 'Setting\Bundle\ToolBundle\Entity\PaymentCard',
                'attr'=>array('class'=>'span12 m-wrap invoice-change'),
                'empty_value' => '---Payment card---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))
            ->add('accountBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap invoice-change'),
                'empty_value' => '---Choose Bank Account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))

            ->add('salesBy', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 m-wrap sales-by invoice-change'),
                'empty_value' => '---Sales By---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->where("u.isDelete != 1")
                        ->andWhere("u.enabled = 1")
                        ->andWhere("u.domainOwner = 2")
                        ->andWhere("u.globalOption =".$this->globalOption->getId())
                        ->orderBy("u.username", "ASC");
                }
            ))

            ->add('accountMobileBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap invoice-change'),
                'empty_value' => '---Choose Mobile Account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Terminalbd\PosBundle\Entity\Pos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pos';
    }

}
