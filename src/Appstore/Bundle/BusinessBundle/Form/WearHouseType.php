<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class WearHouseType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter wear house name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter wear house name'))
                ))
            )
            ->add('shortCode','text', array('attr'=>array('class'=>'m-wrap span12','maxlength'=>3,'autocomplete'=>'off','placeholder'=>'Enter short code 3 digit'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter short code 3 digit'))
                ))
            );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\WearHouse'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wearhouse';
    }


}
