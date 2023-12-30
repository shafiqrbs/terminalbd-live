<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ContentBundle\Form\ContactOpeningType;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Setting\Bundle\ToolBundle\Form\SiteSettingType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SocialIconType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


            $builder


                ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter  e-mail address')))
                ->add('webMail','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter web mail url')))
                ->add('facebookPageUrl','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter facebook page url')))
                ->add('twitterUrl','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter web twitter url')))
                ->add('instagramPageUrl','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter instagram url')))
                ->add('googlePlus','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter google plus url')))
                ->add('youtube','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter youtube url')))
                ->add('linkedin','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter linkedin id & url')))
                ->add('skype','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter skype id & url')))
              ;



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
        return 'social';
    }


}
