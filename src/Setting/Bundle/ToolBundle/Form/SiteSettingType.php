<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteSettingType extends AbstractType
{

    public  $syndicateId;

    public function __construct($syndicateId)
    {
        $this->syndicateId = $syndicateId;

    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder


              ->add('theme', 'entity', array(
                 'required'    => false,
                 'class' => 'Setting\Bundle\ToolBundle\Entity\Theme',
                 'empty_value' => '---Select Theme ---',
                 'property' => 'name',
                 'attr'=>array('class'=>'m-wrap span12'),
                 'query_builder' => function(EntityRepository $er){
                         return $er->createQueryBuilder('wt')
                             ->andWhere("wt.status = 1")
                             /*->andWhere(':syndicate MEMBER OF wt.syndicates')
                             ->setParameter('syndicate', $this->syndicateId)*/
                             ->orderBy('wt.name','ASC');
                 },
              ))

              /*->add('syndicateModules', 'entity', array(
                  'required'      => true,
                  'expanded'      =>true,
                  'multiple'      =>true,
                  'class' => 'Setting\Bundle\ToolBundle\Entity\SyndicateModule',
                  'property' => 'name',
                  'attr'=>array('class'=>'check-list span12'),
                  'query_builder' => function(EntityRepository $er){
                          return $er->createQueryBuilder('sm')
                              ->andWhere("sm.status = 1")
                              ->andWhere(':syndicate MEMBER OF sm.syndicates')
                              ->orderBy('sm.name','ASC')
                              ->setParameter('syndicate', $this->syndicateId);
                      },

              ))*/

            ->add('modules', 'entity', array(
                'required'      => true,
                'expanded'      =>true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Module',
                'property' => 'name',
                'attr'=>array('class'=>'check-list span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('m')
                            ->where("m.status = 1")
                            ->andWhere("m.slug != 'contact'")
                            ->andWhere("m.slug != 'page'")
                            ->orderBy('m.name','ASC');
                    },
            ))

            ->add('appModules', 'entity', array(
                'required'      => true,
                'expanded'      =>true,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\AppModule',
                'property' => 'name',
                'attr'=>array('class'=>'check-list span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('m')
                            ->andWhere("m.status = 1")
                            ->orderBy('m.name','ASC');
                },
            ));


    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\SiteSetting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_sitesetting';
    }
}
