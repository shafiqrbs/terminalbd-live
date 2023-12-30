<?php

namespace Setting\Bundle\ToolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TemplatingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('backgroundColor')
            ->add('headerColor')
            ->add('footerColor')
            ->add('bodyColor')
            ->add('menuColor')
            ->add('menuLiColor')
            ->add('menuLiHoverColor')
            ->add('siteFontSize')
            ->add('siteFontFamily')
            ->add('anchorColor')
            ->add('anchorHoverColor')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\Templating'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_templating';
    }
}
