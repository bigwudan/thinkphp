<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function actionCheckNum()
    {
        $num = intval($_GET['num']);
        $checkNum = intval($_GET['checknum']);
        if (!$num || !$checkNum) {
            die('1');
        }
        $ticketData = M('ticket')->where("number = {$num} AND checknum = {$checkNum}")->find();
        if (!$ticketData) {
            die('2');
        }
        if ($ticketData['is_spend'] == 1) {
            die('3');
        } else {
            die('4');
        }
    }

    public function actionSubmit()
    {
        $num = intval(I('number'));
        $checkNum = intval(I('checknum'));
        if (!$num || !$checkNum) {
            die('填写正确卷号或验证码');
        }
        $ticketData = M('ticket')->where("number = {$num} AND checknum = {$checkNum}")->find();
        if (!$ticketData) {
            die('填写正确卷号或验证码');
        }
        if ($ticketData['is_spend'] == 1) {
            die('该卷号已经消费');
        } else {
            $ip = $_SERVER["REMOTE_ADDR"] ? $_SERVER["REMOTE_ADDR"] : '0';
            $data = M('ticket')->where("id = {$ticketData['id']}")->limit('1')->save(array('is_spend' => 1, 'time' => time(), 'ip' => $ip));
            if (!$data) {
                die('更新失败');
            }
            $token = md5(time());
            session('token', $token);
            $this->assign('token', $token);
            $this->assign('id', $ticketData['id']);
            $this->display('/writeaddr');
        }
        die('');
    }


    public function actionSucess()
    {
        $token = I('token');
        $id = intval(I('id'));
        $rec_name = strip_tags(trim(I('rec_name')));
        $rec_addr = strip_tags(trim(I('rec_addr')));
        $rec_tel = intval(I('rec_tel'));
        $remark = strip_tags(trim(I('remark')));
        $sessToken = session('token');
        if (!$sessToken) {
            die('登录异常');
        }
        if ($sessToken === $token) {
            $upData = array(
                'rec_name' => $rec_name,
                'rec_addr' => $rec_addr,
                'rec_tel'  => $rec_tel,
                'remark'   => $remark
            );
            $data = M('ticket')->where("id = {$id}")->limit('1')->save($upData);
            session('token', null);
            if (!$data) {
                die('更新失败');
            } else {
                $this->display('/sucess');
            }
        } else {
            session('token', null);
            die('登陆异常');
        }

    }

    public function actionSave(){
        $recordData = \Vendor\Rbac\MyRbac::saveAccessList(1);
        var_dump($recordData);
    }

    public function actionTest(){

        \Think\Log::write('测试日志信息，这是警告级别，并且实时写入','WARN');
        die('wudan');
    }

}