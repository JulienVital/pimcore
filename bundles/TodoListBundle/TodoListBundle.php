<?php

namespace TodoListBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class TodoListBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/todolist/js/pimcore/startup.js'
        ];
    }
}