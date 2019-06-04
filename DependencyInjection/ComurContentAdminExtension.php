<?php


namespace Comur\ContentAdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class ComurContentAdminExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('comur_content_admin.locales', $config['locales']);
        $container->setParameter('comur_content_admin.enable_comur_image_bundle', $config['enable_comur_image_bundle']);
        $container->setParameter('comur_content_admin.templates', $config['templates']);
    }

    /* to add some parameters in case of ComurImageBundle is enabled */
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');

        // process the configuration of ComurContentAdminBundle
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        // determine if ComurImageBundle is registered
        if (isset($bundles['ComurImageBundle'])) {
            $container->prependExtensionConfig('comur_content_admin', array(
                'enable_comur_image_bundle' => true
            ));

            // Needed in template to activate or not ComurImageBundle
            $container->prependExtensionConfig('twig', array(
                'globals' => array(
                    'enableComurImageBundle' => true
                )
            ));

        }

        // Add theme in form themes of twig

        $container->prependExtensionConfig('twig', array(
            'form_themes' => array(
                '@ComurContentAdmin/inline_content_field.html.twig'
            )
        ));
    }

}
