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
$sc->register('framework', 'Itav\FrameService')
    ->setArguments(array(new Reference('dispatcher'), new Reference('resolver')))
;

return $sc;