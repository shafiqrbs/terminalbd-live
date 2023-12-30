<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{

    private $globalOption;

    /** @var  LocationRepository */
    private $location;


    public function __construct($globalOption, LocationRepository $location)
    {
        $this->globalOption = $globalOption;
        $this->location = $location;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter title'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('organization','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter organization name')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email address')))
            ->add('website','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter website')))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'Enter event price')))
            ->add('content','textarea', array('attr'=>array('class'=>'ckeditor m-wrap span12','rows' => 12)))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12','rows' => 9)))
            ->add('additionalPhone','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('file','file', array('attr'=>array('class'=>'default')))
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
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A','placeholder'=>'Start hour')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10', 'data-format' => 'hh:mm A', 'placeholder'=>'End hour')))
            ->add('photo_gallery', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\MediaBundle\Entity\PhotoGallery',
                'empty_value' => '---Select Photo Gallery---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('o')
                            ->andWhere("o.status = 1")
                            ->andWhere("o.globalOption = $this->globalOption ")
                            ->orderBy('o.name','ASC');
                    },
            ))
            ->add('latitude','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('longitude','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select customer location'))
                ),
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))
            ->add('eventType', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'empty_value' => '---Select Event Type ---',
                'expanded'      =>false,
                'required'    => false,
                'multiple'      =>false,
                'choices' => array('Ongoing' => 'Ongoing',  'Upcoming' => 'Upcoming'),
            ))
            ->add('moduleCategory', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ContentBundle\Entity\ModuleCategory',
                'empty_value' => '---Select event category ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('o')
	                    ->join('o.module', 'module')
	                    ->where("o.status = 1")
	                    ->andWhere("module.slug = :module")
	                    ->setParameter('module', 'event')
                        ->andWhere("o.globalOption =".$this->globalOption)
                        ->orderBy('o.name','ASC');
                },
            ));
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

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}
