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

$sc = new DependencyInjection\ContainerBuilder();
$sc->setParameter('charset', 'UTF-8');
$sc->setParameter('routes', include __DIR__.'/routing.php');

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


//use Symfony\Component\Form\Forms;
//use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
//use Symfony\Component\Translation\Translator;
//use Symfony\Component\Translation\Loader\XliffFileLoader;
//use Symfony\Bridge\Twig\Extension\TranslationExtension;
//use Symfony\Component\HttpFoundation\Session\Session;
//use Symfony\Component\Security\Extension\Csrf\CsrfExtension;
//use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
//use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
//use Symfony\Component\Security\Csrf\CsrfTokenManager;
//use Symfony\Bridge\Twig\Extension\FormExtension;
//use Symfony\Bridge\Twig\Form\TwigRenderer;
//use Symfony\Bridge\Twig\Form\TwigRendererEngine;
//
//
//
//        $this->container['translator'] = function($c) {
//            $translator = new Translator('en');
//            $translator->addLoader('xlf', new XliffFileLoader());
//            $translator->addResource(
//                    'xlf', __DIR__ . '/../messages.en.xlf', 'en'
//            );
//            return $translator;
//        };
//
//        $this->container['form_default_theme'] = 'form_div_layout.html.twig';
//        $this->container['vendor_dir'] = realpath(__DIR__ . '/../../vendor');
//
//        $this->container['app_variable_reflection'] = function($c) {
//            return new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
//        };
//        $this->container['vendor_twig_bridge_dir'] = function($c){
//            return dirname($c['app_variable_reflection']->getFileName());
//        };
//
//        $this->container['views_dir'] = $viewsDir = realpath(__DIR__ . '/../');
//
//        $this->container['twig'] = function($c) {
//            return new \Twig_Environment(new \Twig_Loader_Filesystem(array(
//                $c['views_dir'],
//                $c['vendor_twig_bridge_dir'] . '/Resources/views/Form',
//            )));
//        };
//        
//        $this->container['form_engine'] = function($c){
//            $formEngine = new TwigRendererEngine(array($c['form_default_theme']));
//            $formEngine->setEnvironment($c['twig']);
//            return $formEngine;
//        };
//
//        $this->container['twig']->addExtension(new FormExtension(new TwigRenderer($this->container['form_engine'], null)));
//        $this->container['twig']->addExtension(new TranslationExtension($this->container['translator']));
//
//        $this->container['form_factory'] = function($c){
//            return Forms::createFormFactoryBuilder()
//                ->addExtension(new HttpFoundationExtension())
//                ->getFormFactory();
//        };





return $sc;