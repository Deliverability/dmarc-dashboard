<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function init()
    {
        date_default_timezone_set('UTC');
        parent::init();
    }

    public function registerBundles()
    {
        $bundles = array(
            // Framework base
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),

            // Doctrine ORM and migrations
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Avanzu\Doctrine\PrefixBundle\AvanzuDoctrinePrefixBundle(),

            // Imported functionality components
            new FOS\UserBundle\FOSUserBundle(),

            // Frontend components
            new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new Ob\HighchartsBundle\ObHighchartsBundle(),

            // Supplemental components
            new Trend404\Trend404Bundle\Trend404Bundle(),

            // Main application
            new DmarcDash\DmarcDash(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getCacheDir()
    {
        return $this->rootDir.'/../var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return $this->rootDir.'/../var/logs/' . $this->getEnvironment();
    }
}
