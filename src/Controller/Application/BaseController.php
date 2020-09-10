<?php


namespace App\Controller\Application;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController{
    public function defaultRender(){
        return [
            'title' => 'Список приложений',
            'main_title' => 'Список всех найденых приложений',
        ];
    }
}