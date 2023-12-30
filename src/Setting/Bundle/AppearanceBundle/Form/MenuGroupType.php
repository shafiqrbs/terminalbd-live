<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MenuGroupType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'form-control input-sm','placeholder'=>'Enter menu group name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('groupType','text', array('attr'=>array('class'=>'form-control input-sm','placeholder'=>'Enter menu group name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('syndicates', 'entity', array(
                'required'      => true,
                'expanded'      =>true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Syndicate',
                'property' => 'name',
                'attr'=>array('class'=>''),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->andWhere("s.status = 1")
                        ->andWhere("s.level = 1")
                        ->orderBy('s.name','ASC');
                },
            ))
            ->add('groupType', 'choice', array(
                'choices' => array('Website' => 'Website', 'Mobile' => 'Mobile'),
                'expanded'=>true,
                'multiple'=>true,

            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\MenuGroup'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_appearancebundle_menugroup';
    }
}
