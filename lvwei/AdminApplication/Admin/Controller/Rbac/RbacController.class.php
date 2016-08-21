<?php
namespace Admin\Controller\Rbac;

use Think\Controller;

class RbacController extends Controller
{
    public function actionOperationUser(){
        $user = new \Admin\Controller\Body\BodyController();
        $mainHeadHtml = $user->operationHead();
        $userInfoDataDB = M('user')->select();
        $this->assign('head' , $mainHeadHtml);
        $this->assign('userInfoDataDB' , $userInfoDataDB);
        $this->display('/Rbac/Rbac');
    }

    /**
     * ajax处理
     */
    public function actionAjaxOperationFactory(){
        $resquestData = I('get.');
        if($resquestData['mode'] == 'operationUser'){
            $response = $this->_operationUser($resquestData);
        }elseif($resquestData['mode'] == 'operationnode'){
            $response = $this->_AllNodeList($resquestData);
        }elseif($resquestData['mode'] == 'modifyusernode'){

            $response = $this->_RoleToNodeDB($resquestData);
            //var_dump(explode(',' , $resquestData['content']));
        }
        die('');
    }

    /**
     * 操作管理员
     * @param array get请求过来的数据
     * return string
     */
    private function _operationUser($varResquestData){
        $id = intval($varResquestData['userid']);
        $userOneData = M('user')->where("id = {$id}")->find();
        $userOneData['status'] = ($userOneData['status'] == 1) ? 0 : 1;
        $resultData = $varResquestData['opertype'] == 'del' ? M('user')->where("id = {$id}")->limit(1)->delete() :
                                                M('user')->where("id = {$id}")->limit(1)->save(array('status' => $userOneData['status']));
        return $resultData;
    }

    /**
     * 表单修改增加管理员账号
     */
    public function actionOperationUserToDB(){
        $userOneDB = array(
            'username' => trim(I('username')),
            'password' => md5(trim(I('username'))),
        );
        if(I('id')){
            $id = intval(I('id'));
            $callBackDB = M('user')->where("id = {$id}")->limit(1)->save($userOneDB);
        }else{
            $userNum = M('user')->where("username = '{$$userOneDB['username']}'")->count();
            !$userNum && $this->error('重复登录名');
            $callBackDB = M('user')->add($userOneDB);
        }
        if($callBackDB){
            $this->success('操作完成');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 管理角色
     */
    public function actionOperationRole(){
        $user = new \Admin\Controller\Body\BodyController();
        $mainHeadHtml = $user->operationHead();
        $roleInfoDataDB = M('role')->select();
        $this->assign('head' , $mainHeadHtml);
        $this->assign('roleInfoDataDB' , $roleInfoDataDB);
        $this->display('/Rbac/OperationRole');
    }


    /**
     * 显示节点
     * @param $varData 数据
     */
    private function _AllNodeList($varData){
        $roleId = intval($varData['id']);
        $access = M('access')->where("role_id={$roleId}")->select();
        $nodeData = M('node')->order('sort')->select();
        $nodeData = $this->_iterationTree($nodeData);
        foreach ($nodeData as $k => $v) {
            foreach ($access as $subK => $subV) {
                if($subV['node_id'] == $v['id']){
                    $nodeData[$k]['checked'] = 1;
                    break;
                }
            }
        }
        $responseHtml = '';
        foreach($nodeData as $k => $data){
            $checkStr = '';
            $actionNameStr = '';
            $data['checked'] == 1 && $checkStr = 'checked="checked"';
            if($data['checked'] === 1){
                $actionNameStr = '[项目]';
            }elseif($data['checked'] === 2){
                $actionNameStr = '[模块]';
            }else{
                $actionNameStr = '[操作]';
            }
            $responseHtml .=<<<EOT
<tr data-level="{$data['level']}">
    <td>
        <div style="margin-left: {$data['level']}0px" class="checkbox">
            <label>
                <input $checkStr  name="nodeid[]" class="input-checkbox" type="checkbox" value="{$data['id']}" data-level="{$data['level']}" onclick="OperationAdmin.checkTreeControl(this)">
                $actionNameStr
                {$data['title']}
            </label>
        </div>
    </td>
</tr>
EOT;
        }
        die($responseHtml);
    }

    /**
     * 显示节点
     * @param $varData 数据
     */
    public function _RoleToNodeDB($varData){
        $roleId = intval($varData['id']);
        $access = M("access"); // 实例化User对象
        $dataList = array();
        $access->where("role_id={$roleId}")->delete(); // 删除id为5的用户数据
        $varData['content'] = explode(',' , $varData['content']);
        foreach ($varData['content'] as $k => $v) {
            $dataList[] = array('role_id'=>$roleId,'node_id'=>$v);
        }
        $response = $access->addAll($dataList);
        return $response;
    }

    /**
     * 迭代 tree
     * @param array $varData
     * @param string $varParent
     * @param string $varChildren
     * @param int $varPVal
     */
    private function _iterationTree($varData , $varPVal = 0 , $varParent = 'pid' , $varChildren = 'id' ){
        $data = $varData;
        $id = $varChildren;
        $parent_list = array($varPVal);
        $resData = array();
        $num = 0;
        while(!empty($data) && !empty($parent_list)){
            $isFlag = 0;
            $curPid = end($parent_list);
            foreach ($data as $k => $v) {
                if($v[$varParent] == $curPid){
                    array_push($parent_list, $v[$id]);
                    array_push($resData, $v);
                    $isFlag = 1;
                    unset($data[$k]);
                    break;
                }
            }
            if($isFlag == 0) array_pop($parent_list);
            $num++ ;
        }
        return $resData;
    }

    /**
     * 测试
     */
    public function actionTest(){
        $this->display('/Rbac/Test');
    }




}