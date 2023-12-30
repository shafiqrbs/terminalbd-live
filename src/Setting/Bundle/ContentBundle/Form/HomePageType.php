<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class HomePageType extends AbstractType
{

    private $globalOption;
    private $siteSettingId;

    public function __construct($globalOption,$siteSettingId)
    {
        $this->globalOption = $globalOption;
        $this->siteSettingId = $siteSettingId;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter name '),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('showAbout')
            ->add('featureText','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>4)))
            ->add('content','textarea', array('attr'=>array('class'=>'wysihtml5 m-wrap span12','rows'=>10)))
            /*
           ->add('file','file', array('attr'=>array('class'=>'default')))
           ->add('photo_gallery', 'entity', array(
               'required'    => false,
               'class' => 'Setting\Bundle\MediaBundle\Entity\PhotoGallery',
               'empty_value' => '---Select Photo Gallery---',
               'property' => 'name',
               'attr'=>array('class'=>'m-wrap select2 span12'),
               'query_builder' => function(EntityRepository $er){
                       return $er->createQueryBuilder('o')
                           ->andWhere("o.status = 1")
                           ->andWhere("o.globalOption = $this->globalOption")
                           ->orderBy('o.name','ASC');
                   },
           ))

          ->add('syndicateModules', 'entity', array(
               'required'      => true,
               'expanded'      =>true,
               'multiple'      =>true,
               'class' => 'Setting\Bundle\ToolBundle\Entity\SyndicateModule',
               'property' => 'name',
               'attr'=>array('class'=>'check-list span12'),
               'query_builder' => function(EntityRepository $er){
                       return $er->createQueryBuilder('sm')
                           ->where("sm.status = 1")
                           ->andWhere("sm.isHome = 1")
                           ->andWhere(':siteSetting MEMBER OF sm.siteSettings')
                           ->setParameter('siteSetting',$this->siteSettingId)
                           ->orderBy('sm.name','ASC');

                   },

           ))
           ->add('modules', 'entity', array(
               'required'      => true,
               'expanded'      =>true,
               'multiple'      =>true,
               'class' => 'Setting\Bundle\ToolBundle\Entity\Module',
               'property' => 'name',
               'attr'=>array('class'=>'check-list span12'),
               'query_builder' => function(EntityRepository $er){
                       return $er->createQueryBuilder('sm')
                           ->where("sm.status = 1")
                           ->andWhere("sm.isHome = 1")
                           ->andWhere(':siteSetting MEMBER OF sm.siteSettings')
                           ->setParameter('siteSetting',$this->siteSettingId)
                           ->orderBy('sm.name','ASC');
                   },

           ))
           ->add('showingListing', 'choice', array(
               'choices'  => array(3 => 3, 5 => 5),
               'attr'=>array('class'=>'radio-list'),
               'expanded' => true,
               'multiple' => false,
               'data'     => 1

           ))*/

        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\HomePage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_homepage';
    }
}
