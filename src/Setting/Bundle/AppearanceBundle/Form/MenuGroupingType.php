<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuGroupingType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

           /* ->add('menuGroup', 'entity', array(
                'empty_value' => '---Select Menu Group---',
                'required'    => true,
                'attr'=>array('class'=>'form-control input-sm select2'),
                'class' => 'SettingAppearanceBundle:MenuGroup',
                'property' => 'name'
            ))*/
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\MenuGrouping'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_appearancebundle_menugrouping';
    }
}
