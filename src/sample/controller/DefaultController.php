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
    
    public function page1Action(Request $request)
    {
//        $twig = $this->get('twig');
//        $viewData = [
//            'title' => 'Title is Page1',
//        ];
//        return $this->get('twig')->render('customer/view/origin/list.html.twig', $viewData);        
        
        return new Response("Page1. It is working");
    }    
}