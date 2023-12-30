<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Divi\AjaxLoginBundle\DiviAjaxLoginBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new Knp\Bundle\TimeBundle\KnpTimeBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Core\UserBundle\UserBundle(),
            new Setting\Bundle\LocationBundle\SettingLocationBundle(),
            new Setting\Bundle\MediaBundle\SettingMediaBundle(),
            new Setting\Bundle\AppearanceBundle\SettingAppearanceBundle(),
            new Setting\Bundle\ContentBundle\SettingContentBundle(),
            new Setting\Bundle\ToolBundle\SettingToolBundle(),
            new Gregwar\ImageBundle\GregwarImageBundle(),
            new Product\Bundle\ProductBundle\ProductProductBundle(),
            new Frontend\FrontentBundle\FrontendBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new FOS\CommentBundle\FOSCommentBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new EightPoints\Bundle\GuzzleBundle\GuzzleBundle(),
            new Bindu\BinduBundle\BinduBundle(),
            new Appstore\Bundle\InventoryBundle\InventoryBundle(),
            new Appstore\Bundle\DomainUserBundle\DomainUserBundle(),
            new Appstore\Bundle\AccountingBundle\AccountingBundle(),
            new Xiidea\Bundle\DomainBundle\XiideaDomainBundle(),
            new Appstore\Bundle\CustomerBundle\CustomerBundle(),
            new Appstore\Bundle\HospitalBundle\HospitalBundle(),
            new Appstore\Bundle\HumanResourceBundle\HumanResourceBundle(),
            new Appstore\Bundle\RestaurantBundle\RestaurantBundle(),
            new Appstore\Bundle\MedicineBundle\MedicineBundle(),
            new Appstore\Bundle\BusinessBundle\BusinessBundle(),
            new Appstore\Bundle\DoctorPrescriptionBundle\DoctorPrescriptionBundle(),
            new Terminalbd\PosBundle\PosBundle(),
            new Terminalbd\ReportBundle\ReportBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }
        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

}
