<?php

namespace Setting\Bundle\ToolBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MedicineEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('medicineForm','text', array('attr'=>array('class'=>'m-wrap span12 select2MedicineForm','placeholder'=>'Form Ex:tablet,capsule,injection'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('strength','text', array('attr'=>array('class'=>'m-wrap span12 select2Strength','placeholder'=>'Strength Ex:500mg/10ml'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('packSize','text', array('attr'=>array('class'=>'m-wrap span12 select2PackSize','placeholder'=>'Pack size ex:100s pack')))
            ->add('file');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\MedicineBundle\Entity\MedicineBrand'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'medicine';
    }
}
