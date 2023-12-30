<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CourseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))

                )
            ))
            ->add('courseLevels', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'expanded'      =>true,
                'class'         => 'Setting\Bundle\ToolBundle\Entity\CourseLevel',
                'property'      => 'name',
                'attr'          =>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                                            return $er->createQueryBuilder('c')
                                            ->andWhere("c.status = 1")
                                            ->orderBy('c.id','ASC');
                                        }
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
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\Course'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_course';
    }
}
