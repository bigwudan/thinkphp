<?php
namespace Admin\Controller\Body;

use Think\Controller;

class BodyController extends Controller
{
    public function operationHead(){

        $this->assign('active' , 'class="active"');
        $mainHeadHtml = $this->fetch('/Body/head');


        return $mainHeadHtml;
    }
}