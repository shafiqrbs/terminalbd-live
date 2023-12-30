<?php

namespace Product\Bundle\ProductBundle\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CollectionType extends AbstractType
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
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>5)))
            ->add('discountPercentage','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter percentage float value')))
            ->add('isFeature')
            ->add('status')
            ->add('parent', 'entity', array(
                'required'    => false,
                'class' => 'Product\Bundle\ProductBundle\Entity\Collection',
                'empty_value' => '---Select parent collection---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span8 selectbox'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('c')
                            ->andWhere("c.status = 1")
                            ->orderBy('c.name','ASC');
                }
            ))

            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Product\Bundle\ProductBundle\Entity\Collection'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product_bundle_productbundle_collection';
    }
}
