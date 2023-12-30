<?php

namespace Setting\Bundle\ToolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FooterSettingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('copyRight')
            ->add('displayHomePage')
            ->add('displaySubPage')
            ->add('displayIcon')
            ->add('phoneDisplayHomePage')
            ->add('phoneDisplaySubPage')
            ->add('phoneDisplayIcon')
            ->add('webDisplayHomePage')
            ->add('webDisplaySubPage')
            ->add('turnOff')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\FooterSetting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_footersetting';
    }
}
