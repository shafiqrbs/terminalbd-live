<?php

namespace Setting\Bundle\ToolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdsToolType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('googleServiceID')
            ->add('slotID')
            ->add('siteName')
            ->add('keyword')
            ->add('metaDescription')
            ->add('googleVerificationCode')
            ->add('redirectLang')
            ->add('redirectCode')
            ->add('statCounterID')
            ->add('statCounterSecurityCode')
            ->add('statCounterVisible')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\AdsTool'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_adstool';
    }
}
