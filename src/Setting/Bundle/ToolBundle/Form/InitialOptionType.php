<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class InitialOptionType extends AbstractType
{

    /** @var  SyndicateRepository */
    private $em;
    /** @var  LocationRepository */
    private $location;

    function __construct(SyndicateRepository $em , LocationRepository $location)
    {
        $this->em = $em;
        $this->location = $location;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


            $builder

                ->add('name','text', array('required' => false,'attr'=>array('class'=>'m-wrap tooltips span12','placeholder'=>'Enter your business name' , 'data-original-title' =>'Please enter name of organization' , 'data-trigger' => 'hover'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please organization name required')),
                        new Length(array('max'=>200))
                    )
                ))

                ->add('syndicate', 'entity', array(
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter you business type'))
                    ),
                    'required'    => true,
                    'attr'=>array('class'=>'select2 span12'),
                    'class' => 'Setting\Bundle\ToolBundle\Entity\Syndicate',
                    'choices'=> $this->SyndicateChoiceList(),
                    'choices_as_values' => true,
                    'choice_label' => 'nestedLabel',
                ))

                ->add('mainApp', 'entity', array(
                    'required'    => true,
                    'class' => 'Setting\Bundle\ToolBundle\Entity\AppModule',
                    'empty_value' => '---Select Main Application ---',
                    'property' => 'name',
                    'attr'     =>array('id' => '' , 'class' => 'm-wrap span12'),
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('s')
                            ->andWhere("s.status = 1")
                            ->orderBy('s.name','ASC');
                    },
                ))
                ->add('location', 'entity', array(
                    'required'    => false,
                    'empty_value' => '---Select Location---',
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter location'))
                    ),
                    'attr'=>array('class'=>'select2 span12'),
                    'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                    'choices'=> $this->LocationChoiceList(),
                    'choices_as_values' => true,
                    'choice_label' => 'nestedLabel',
                ));

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\GlobalOption'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'globaloption';
    }

    /**
     * @return mixed
     */
    protected function SyndicateChoiceList()
    {
        return $syndicateTree = $this->em->getSyndicateOptionGroup();

    }

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}


