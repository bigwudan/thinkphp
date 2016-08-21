<?php
namespace Home\Controller\Test;

use Think\Controller;

class TestController extends Controller
{



    public function actionTest(){
        var_dump( "mode = ". MODULE_NAME . " contr = " . CONTROLLER_NAME . " act = " . ACTION_NAME);
        die('wudan');
    }

}