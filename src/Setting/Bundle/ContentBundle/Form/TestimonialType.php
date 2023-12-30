<?php

namespace Setting\Bundle\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TestimonialType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter full name'),
                'constraints' =>array(new NotBlank(array('message'=>'Please input required')))
            ))
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter designation'),
                'constraints' =>array(new NotBlank(array('message'=>'Please input required')))
            ))
            ->add('organization','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter organization name'),
                'constraints' =>array( new NotBlank(array('message'=>'Please input required')))
            ))
            ->add('website','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter website'),
                'constraints' =>array(new NotBlank(array('message'=>'Please input required')))
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>5)));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_page';
    }
}
