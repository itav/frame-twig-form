<?php
/**
 * Created by PhpStorm.
 * User: Magdalena
 * Date: 2016-01-13
 * Time: 22:28
 */
// example.com/src/container.php
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Extension\Csrf\CsrfExtension;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;

$sc = new DependencyInjection\ContainerBuilder();
$sc->setParameter('charset', 'UTF-8');
$sc->setParameter('routes', include __DIR__.'/routing.php');

$sc->setParameter('default-language', 'en');
$sc->setParameter('form_default_theme', 'form_div_layout.html.twig');
$sc->setParameter('vendor_dir', realpath(__DIR__ . '/../vendor'));

$sc->setParameter('app_variable_reflection', new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable'));
$appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
$sc->setParameter('vendor_twig_bridge_dir', dirname( $appVariableReflection->getFileName()));

$sc->setParameter('views_dir', realpath(__DIR__ . '/../'));


$sc->register('context', 'Symfony\Component\Routing\RequestContext');
$sc->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments(array('%routes%', new Reference('context')))
;

$sc->register('resolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');

$sc->register('request.stack', 'Symfony\Component\HttpFoundation\RequestStack');
$sc->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
    ->setArguments(array(new Reference('matcher'), new Reference('request.stack')))
;
$sc->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
    ->setArguments(array('%charset%'))
;
$sc->register('listener.exception', 'Symfony\Component\HttpKernel\EventListener\ExceptionListener')
    ->setArguments(array('Itav\\Sample\\Controller\\ErrorController::exceptionAction'))
;
$sc->register('listener.string_response', 'Itav\StringResponseListener');
$sc->register('listener.content_length', 'Itav\ContentLengthListener');

$sc->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
    ->addMethodCall('addSubscriber', array(new Reference('listener.router')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.response')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.exception')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.string_response')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.content_length')))
;
$sc->register('framework', 'Itav\FrameTwigForm')
    ->setArguments(array(new Reference('dispatcher'), new Reference('resolver')))
;

$sc->register('xliff_file_loader',  'Symfony\Component\Translation\Loader\XliffFileLoader');
$sc->register('translator',  'Symfony\Component\Translation\Translator')
    ->setArguments(array('%default-language%'))
    ->addMethodCall('addLoader', array('xlf', new Reference('xliff_file_loader')))
    ->addMethodCall('addResource', array('xlf', __DIR__ . '/../messages.en.xlf', 'en'))
;
$sc->register('twig_loader_filesystem', '\Twig_Loader_Filesystem' )
    ->setArguments(array('%views_dir%', '%vendor_twig_bridge_dir%' . '/Resources/views/Form'));
$twigDefinition = new DependencyInjection\Definition('\Twig_Environment', array(new Reference('twig_loader_filesystem')));
$sc->setDefinition('twig', $twigDefinition);
$sc->register('form_engine', 'Symfony\Bridge\Twig\Form\TwigRendererEngine')
    ->setArguments(array('%form_default_theme%'))
    ->addMethodCall('setEnvironment', array(new Reference('twig')))
;

$this->container['twig']->addExtension(new FormExtension(new TwigRenderer($this->container['form_engine'], null)));
$this->container['twig']->addExtension(new TranslationExtension($this->container['translator']));

$this->container['form_factory'] = function($c){
    return Forms::createFormFactoryBuilder()
        ->addExtension(new HttpFoundationExtension())
        ->getFormFactory();
};





return $sc;