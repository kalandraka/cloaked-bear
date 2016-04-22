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
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // Buses Taller
            new Buseta\CoreBundle\CoreBundle(),
            new Buseta\DataBundle\DataBundle(),
            new Buseta\NomencladorBundle\BusetaNomencladorBundle(),
            new Buseta\BusesBundle\BusetaBusesBundle(),
            new Buseta\TallerBundle\BusetaTallerBundle(),
            new Buseta\BodegaBundle\BusetaBodegaBundle(),
            new Buseta\TemplateBundle\BusetaTemplateBundle(),
            new Buseta\CombustibleBundle\BusetaCombustibleBundle(),
            new Buseta\TransitoBundle\BusetaTransitoBundle(),
            new HatueySoft\SecurityBundle\HatueySoftSecurityBundle(),
            new HatueySoft\UploadBundle\HatueySoftUploadBundle(),
            new HatueySoft\SequenceBundle\HatueySoftSequenceBundle(),
            new HatueySoft\MenuBundle\HatueySoftMenuBundle(),

            // extra bundles
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new JavierEguiluz\Bundle\EasyAdminBundle\EasyAdminBundle(),
            new APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            //$bundles[] = new Elao\WebProfilerExtraBundle\WebProfilerExtraBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
