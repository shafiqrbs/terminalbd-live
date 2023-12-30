<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ServiceType extends AbstractType
{


    private $globalOption;

    public function __construct(GlobalOption $globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

	    $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Enter service name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                )
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'span12 m-wrap','rows'=>8)))
            ->add('icon', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Icon',
                'empty_value' => '---Select icon---',
                'property' => 'name',
                'attr'=>array('class'=>'select2 span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('o')
                        ->where("o.status = 1")
                        ->orderBy('o.name','ASC');
                },
            ))
            ->add('moduleCategory', 'entity', array(
                'required'    => false,
                'expanded'      =>true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ContentBundle\Entity\ModuleCategory',
                'empty_value' => '---Select category---',
                'property' => 'name',
                'attr'=>array('class'=>'check-list  span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('o')
                        ->join('o.module', 'module')
                        ->where("o.status = 1")
                        /* ->andWhere(':module MEMBER OF o.module')*/
                        ->andWhere("module.slug = :module")
                        ->setParameter('module', 'service')
                        ->andWhere("o.globalOption =".$this->globalOption->getId())
                        ->orderBy('o.name','ASC');
                },
            ));
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
