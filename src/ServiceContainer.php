<?php

namespace Itav;

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;

class ServiceContainer {

    private static $container;

    private static function buildContainer() {

        $sc = new DependencyInjection\ContainerBuilder();

        $sc->setParameter('default-language', 'en');
        $sc->setParameter('form_default_theme', 'form_div_layout.html.twig');
        $sc->setParameter('vendor_dir', realpath(__DIR__ . '/../vendor'));

        $sc->setParameter('app_variable_reflection', new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable'));
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $sc->setParameter('vendor_twig_bridge_dir', dirname($appVariableReflection->getFileName()).'/Resources/views/Form');

        $sc->setParameter('views_dir', realpath(__DIR__ ));

        $sc->register('xliff_file_loader', 'Symfony\Component\Translation\Loader\XliffFileLoader');
        $sc->register('translator', 'Symfony\Component\Translation\Translator')
                ->setArguments(array('%default-language%'))
                ->addMethodCall('addLoader', array('xlf', new Reference('xliff_file_loader')))
                ->addMethodCall('addResource', array('xlf', __DIR__ . '/messages.en.xlf', 'en'))
        ;
        $sc->register('twig_loader_filesystem', '\Twig_Loader_Filesystem')
                ->setArguments(array('%views_dir%', '%vendor_twig_bridge_dir%'));

        $twigDefinition = new DependencyInjection\Definition('\Twig_Environment', array(new Reference('twig_loader_filesystem')));
        $sc->setDefinition('twig', $twigDefinition);

        $sc->register('form_engine', 'Symfony\Bridge\Twig\Form\TwigRendererEngine')
                ->setArguments(array(['%form_default_theme%']))
                ->addMethodCall('setEnvironment', array(new Reference('twig')))
        ;
        $sc->register('twig_renderer', 'Symfony\Bridge\Twig\Form\TwigRenderer')
                ->setArguments(array(new Reference('form_engine'), null))
        ;
        $sc->register('form_extension', 'Symfony\Bridge\Twig\Extension\FormExtension')
                ->setArguments(array(new Reference('twig_renderer')))
        ;
        $sc->register('translation_extension', 'Symfony\Bridge\Twig\Extension\TranslationExtension')
                ->setArguments(array(new Reference('translator')))
        ;
        $sc->getDefinition('twig')
                ->addMethodCall('addExtension', array(new Reference('form_extension')))
                ->addMethodCall('addExtension', array(new Reference('translation_extension')))
        ;
        $sc->register('http_foundation_extension', 'Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension');
        $formBuilderDefinition = new DependencyInjection\Definition();
        $formBuilderDefinition->setFactory(array('Symfony\Component\Form\Forms', 'createFormFactoryBuilder'));
        $formBuilderDefinition->addMethodCall('addExtension', array(new Reference('http_foundation_extension')));
        $sc->setDefinition('form_factory_builder', $formBuilderDefinition);

        $formFactoryDefinition = new DependencyInjection\Definition();
        $formFactoryDefinition->setFactory(array(new Reference('form_factory_builder'), 'getFormFactory'));

        $sc->setDefinition('form_factory', $formFactoryDefinition);

        self::$container = $sc;
    }

    public static function getInstance() {
        if (!(self::$container instanceof DependencyInjection\ContainerBuilder)) {
            self::buildContainer();
        }
        return self::$container;
    }

}
