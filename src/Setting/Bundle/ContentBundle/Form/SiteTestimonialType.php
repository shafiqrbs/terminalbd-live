<?php

namespace Setting\Bundle\ContentBundle\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SiteTestimonialType extends AbstractType
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
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('designation','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter person designation')))
            ->add('company','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter company name')))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>5),
                  'constraints' =>array(
                      new NotBlank(array('message'=>'Please input required')),
                      new Length(array('max'=>200))
                  )
	            ))
	        ->add('businessSector', 'choice', array(
		        'attr'=>array('class'=>'m-wrap span12'),
		        'choices' => array('portal' => 'Portal','education' => 'Education',  'ecommerce' => 'E-commerce', 'reservation' => 'Reservation', 'fleet' => 'Fleet Management'),
	        ))
			->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\Testimonial'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'testimonial';
    }
}
