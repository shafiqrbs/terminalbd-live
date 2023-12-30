<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountLoanUserType extends AbstractType
{


    /** @var  GlobalOption */
    private $option;

    function __construct(GlobalOption $option)
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Customer name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter account name'))
                    ))
            )
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','autocomplete'=>'off','placeholder'=>'Mobile no'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter mobile no'))
                    ))
            )
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 ','rows'=>4,'placeholder'=>'Enter customer address')))
            ->add('globalOption','hidden', array('data'=>$this->option->getId()));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountLoanUser'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'loanuser';
    }

}
