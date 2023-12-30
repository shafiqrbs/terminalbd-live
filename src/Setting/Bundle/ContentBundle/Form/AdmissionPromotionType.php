<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdmissionPromotionType extends AbstractType
{

    private $user;
    private $branch;

    public function __construct($user,$branch)
    {
        $this->user = $user;
        $this->branch = $branch;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter payment amount'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('position', 'choice', array(
                'required'      =>true,
                'attr'=>array('class'=>'selectbox'),
                'choices' => array('top' => 'Top',  'right' => 'Right', 'admission' => 'Admission Page'),
            ))
            ->add('paymentStatus', 'choice', array(
                'required'      =>true,
                'attr'=>array('class'=>'selectbox'),
                'choices' => array('Receive' => 'Receive',  'Due' => 'Due','Others'=>'Others'),
            ))
            ->add('promotionStartDate','date', array('attr'=>array('class'=>'selectbox')))
            ->add('promotionEndDate','date', array('attr'=>array('class'=>'selectbox')));


    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\Admission'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_admission';
    }
}
