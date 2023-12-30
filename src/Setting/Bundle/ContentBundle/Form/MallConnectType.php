<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class MallConnectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('shopNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter shop name optional only non registered customer')))

            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('contact','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('level', 'choice', array(
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'attr'=>array('class'=>'select2 span12'),
                'choices' => array(
                    '' => 'Select block',
                    'Level-01' => 'Level-01',
                    'Level-02' => 'Level-02',
                    'Level-03' => 'Level-03',
                    'Level-04' => 'Level-04',
                    'Level-05' => 'Level-05',
                    'Level-06' => 'Level-06',
                    'Level-07' => 'Level-07',
                    'Level-08' => 'Level-08',
                    'Level-09' => 'Level-09',
                    'Level-10' => 'Level-10'
                ),
            ))
            ->add('block', 'choice', array(
                'attr'=>array('class'=>'select2 span12'),
                'choices' => array(
                    '' => '---Select block----',
                    'Block-A' => 'Block-A',
                    'Block-B' => 'Block-B',
                    'Block-C' => 'Block-C',
                    'Block-D' => 'Block-D',
                    'Block-E' => 'Block-E',
                    'Block-F' => 'Block-F',

                ),
            ))
            ->add('mall', 'entity', array(
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'required'      => false,
                'empty_value'   => '--- Select Shopping mall ---',
                'multiple'      =>false,
                'expanded'      =>false,
                'class'         =>'Setting\Bundle\ToolBundle\Entity\GlobalOption',
                'property'      =>'name',
                'attr'          =>array('class'=>'m-wrap span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('d')
                        ->where("d.syndicate = 224")
                        ->orderBy('d.name','ASC');
                }
            ))

            ->add('categories', 'entity', array(
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'required'      => true,
                'expanded'      =>true,
                'multiple'      =>true,
                'class' => 'Product\Bundle\ProductBundle\Entity\Category',
                'property' => 'name',
                'attr'=>array('class'=>''),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->andWhere("s.status = 1")
                        ->andWhere("s.level = 2")
                        ->orderBy('s.name','ASC');
                },
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('isBrand')

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\MallConnect'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_mallconnect';
    }
}
