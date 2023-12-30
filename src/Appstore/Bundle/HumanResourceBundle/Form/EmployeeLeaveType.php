<?php

namespace Appstore\Bundle\HumanResourceBundle\Form;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmployeeLeaveType extends AbstractType
{


    /** @var  GlobalOption */
    private $globalOption;


    function __construct(GlobalOption $globalOption)
    {
        $this->globalOption     = $globalOption;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('leaveSetup', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HumanResourceBundle\Entity\LeaveSetup',
                'property' => 'leaveOffDay',
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Select Leave Type---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select leave type required'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.globalOption ={$this->globalOption->getId()}")
                        ->orderBy("e.name", "ASC");
                }
            ))

            ->add('startDate','text', array('attr'=>array('class'=>'m-wrap span12 date-picker','placeholder'=>'Enter start date'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('endDate','text', array('attr'=>array('class'=>'m-wrap span12 date-picker','placeholder'=>'Enter end date'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HumanResourceBundle\Entity\EmployeeLeave'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hr_leave';
    }


}
