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
use Symfony\Component\Form\Extension\Core\Type;

class DefaultController extends \Itav\AbstractController {

    public function indexAction(Request $request) {
        return new Response("Homepage. It is working");
    }

    public function page1Action(Request $request) {
        $twig = $this->get('twig');
        $viewData = [
            'table' => 'Title is Page1',
        ];
        return $twig->render('sample/view/default/page1.html.twig', $viewData);

        //return new Response("Page1. It is working");
    }

    public function infoAction(Request $request) {

        $formFactory = $this->get('form_factory');
        $form = $formFactory->createBuilder()
                ->add('editButton', Type\ButtonType::class)
                ->add('deleteButton', Type\ButtonType::class)
                ->add('cancelButton', Type\ButtonType::class)
                ->getForm();
        return $this->get('twig')->render(
                        'sample/view/default/info.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

}
