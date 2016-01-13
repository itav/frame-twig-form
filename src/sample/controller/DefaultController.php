<?php

/**
 * Created by PhpStorm.
 * User: Magdalena
 * Date: 2016-01-13
 * Time: 14:04
 */
namespace Itav\Sample\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function indexAction(Request $request)
    {
        return new Response("Homepage. It is working");
    }
}