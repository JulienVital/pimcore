<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TodoListController extends FrontendController
{
    /**
     * @Template
     * @param    Request $request
     * @return   array
     */
    public function defaultAction(Request $request)
    {
        return [];
    }

    public function todoListAction(Request $request)
    {

        return $this->render('todolist/default.html.twig');
    }
}
