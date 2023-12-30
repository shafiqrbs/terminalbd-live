<?php

namespace Setting\Bundle\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class NoticeBoardType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'wysihtml5 m-wrap span12','rows'=>8)))
            ->add('startDate','date', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>''),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'years'=> array('2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'),
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',

            ))
            ->add('endDate','date', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>''),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'years'=> array('2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'),
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',

            ))
            ->add('status')
        ;
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
