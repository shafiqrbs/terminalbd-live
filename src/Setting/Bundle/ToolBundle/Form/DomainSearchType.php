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

class DomainSearchType extends AbstractType
{

    /** @var  SyndicateRepository */
    /** @var  LocationRepository */

    private $em;

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

                ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'search keyword....'),
                    'constraints' =>array()
                ))

                ->add('syndicate', 'entity', array(
                    'required'    => true,
                    'empty_value' => '---Select Business Sector---',
                    'attr'=>array('class'=>'select2 span12'),
                    'class' => 'Setting\Bundle\ToolBundle\Entity\Syndicate',
                    'choices'=> $this->SyndicateChoiceList(),
                    'choices_as_values' => true,
                    'choice_label' => 'nestedLabel',
                ))


                ->add('location', 'entity', array(
                    'required'    => false,
                    'empty_value' => '---Select Location---',
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
        return 'search';
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
        return $this->location->getLocationOptionGroup();

    }
}


