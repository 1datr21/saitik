<?php

namespace app\controllers;

class VueController extends AbstractController
{
    public function run()
    {
        return $this->renderTemplate('./views/vue/vue_page.php');
    }

    public function invbook()
    {
        return $this->renderTemplate('./views/vue/inv_book.php');
    }
}