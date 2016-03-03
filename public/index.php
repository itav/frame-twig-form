<?php
require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
$sc = include __DIR__.'/../src/container.php';

$start = round(microtime(true) * 1000);
$request = Request::createFromGlobals();
$response = $sc->get('framework')->handle($request);
$stop = round(microtime(true) * 1000);
print_r([$stop - $start]);
$response->send();