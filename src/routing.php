<?php
/**
 * Created by PhpStorm.
 * User: Magdalena
 * Date: 2016-01-13
 * Time: 11:37
 */
use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add('homepage', new Routing\Route('/', array(
    '_controller' => 'Itav\Sample\Controller\DefaultController::indexAction',
)));
$routes->add('page1', new Routing\Route('/page1', array(
    '_controller' => 'Itav\Sample\Controller\DefaultController::page1Action',
)));
$routes->add('info', new Routing\Route('/info', array(
    '_controller' => 'Itav\Sample\Controller\DefaultController::infoAction',
)));
return $routes;