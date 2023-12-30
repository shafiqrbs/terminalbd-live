<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MenuCustomType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('menu','text', array('attr'=>array('class'=>'form-control span12','placeholder'=>'Enter menu name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('slug','text', array('attr'=>array('class'=>'form-control span12','placeholder'=>'Enter menu slug name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\MenuCustom'
        ));
    }

    /**
     * @return string
     */
    public function getMenu()
    {
        return 'setting_bundle_appearancebundle_menucustom';
    }
}
