<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BrandingType extends AbstractType
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
            ->add('customUrl','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter custom url')))
            ->add('categories', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'class' => 'Product\Bundle\ProductBundle\Entity\Category',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('c')
                            ->andWhere("c.status = 1")
                            ->andWhere("c.level < 3")
                            ->orderBy('c.name','ASC');
                    },
            ))


            ->add('type', 'choice', array(
                'attr' =>array('class'=>'selectbox'),
                'choices'   => array(
                    'national'     => 'National',
                    'international'    => 'International'
                ),
                'multiple'  => false,
            ))

            ->add('file')
            ->add('sponsor')
            ->add('status')
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\Branding'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_branding';
    }
}
