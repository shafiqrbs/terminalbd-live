<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class WebThemeType extends AbstractType
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
                        new NotBlank(array('message'=>'Please input required')),
                        new Length(array('max'=>200))
                    )
                ))
                ->add('folderName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter name'),
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
                    'attr'=>array('class'=>'selectbox'),
                    'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('s')
                                ->andWhere("s.status = 1")
                                ->andWhere("s.level = 1")
                                ->orderBy('s.name','ASC');
                        },
                ))

                ->add('file')
                ->add('status')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\WebTheme'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_webtheme';
    }
}
