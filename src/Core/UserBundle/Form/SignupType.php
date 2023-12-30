<?php

namespace Core\UserBundle\Form;

use Core\UserBundle\Form\Type\ProfileType;
use Core\UserBundle\Form\Type\SignupProfileType;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Form\InitialOptionType;
use Setting\Bundle\ToolBundle\Form\SiteSettingAppType;
use Setting\Bundle\ToolBundle\Form\SiteSettingType;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class SignupType extends AbstractType
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
        $builder->add('profile', new SignupProfileType());
        $builder->add('globalOption', new InitialOptionType($this->em,$this->location));
      //  $builder->add('siteSetting', new SiteSettingAppType());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User',
            'cascade_validation' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain';
    }
}