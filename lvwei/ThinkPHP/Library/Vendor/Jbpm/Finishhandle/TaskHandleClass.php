<?php
namespace epet\hr\epetworkflow\finishhandle;
/**
 * wudan 吴丹 创建于 2016-09-30 18:37:26
TaskHandleClass
 */
class TaskHandleClass{

    /**
     * transation类型
     */
    private $_transationObj = null;

    /**
     * 初始化
     * @param obj $varTransationObj 类型
     * @param obj $varExecutionObj execution对象
     * @return array
     */
    public function initi($varTransationObj , $varExecutionObj){
        $this->_transationObj = $varTransationObj;
        $execution = $this->_transationObj->getExecution();
        $histActinst = $this->_transationObj->getHistActinst();
        $histActinst['insert']['histask_id'] = $execution['insertExecution']['execution_id'];
        $this->_transationObj->setHistActinst($histActinst);
        $targetXmlAction = $varTransationObj->getTargetXmlAction();
        $assign = null;
        foreach($targetXmlAction['attributeList'] as $k => $v){
            if($v['nodeName'] === 'assingee'){
                $assign = $v['nodeValue'];
                break;
            }
        }
        if(!$assign){
            $handler = $varTransationObj->getTargetXmlAction()['handler'];
            $obj = new $handler();
            $needParticipation = array(
                'group' => $obj->assign($varExecutionObj),
                'user' => '',
                'type' => '',
                'task_id' => $execution['insertExecution']['execution_id']
            );
            $this->_transationObj->setParticipation($needParticipation);
        }
        $needTask = $this->_getNeedTask($assign , $varExecutionObj);
        $this->_transationObj->setTask($needTask);
        $needHistTask = array(
            'histask_id' =>  $execution['insertExecution']['execution_id'],
            'addtime' => time() ,
            'endtime' => 0,
            'duration' => 0 ,
            'assignee' => '' ,
            'stats' => '',
            'outcome' => '' ,
            'execution_id' => $execution['insertExecution']['execution_id'],
        );
        $needHistTask = array(
            'updata' => 0,
            'insert' => $needHistTask
        );
        $this->_transationObj->setHistTask($needHistTask);
        return $this->_transationObj;
    }

    /**
     * 处理assgin函数
     * @param string $varAssgin 字符串
     * @param object $varExecutionObj 对象
     * @return array
     */
    private function _getNeedTask($varAssgin , $varExecutionObj){
        $varList = $varExecutionObj->getVarList();
        $execution = $this->_transationObj->getExecution();
        if($varAssgin){
            $preVariablelist = array();
            if($varList){
                foreach($varList as $k => $v){
                    $tmp = null;
                    $tmp = $v['class'] . "_value";
                    $preVariablelist[$v['key']] = $v[$tmp];
                }
                $matchList = array();
                preg_match_all("/#{(\w*)}/i" , $varAssgin , $matchList);
                $newVars = array();
                foreach($matchList[1] as $k => $v){
                    $newVars[$v] = $preVariablelist[$v];
                    $needTask[] = array(
                        'task_id' => $execution['insertExecution']['execution_id'] ,
                        'activityname' => $execution['insertExecution']['activityname'] ,
                        'assignee' => "{$newVars[$v]}",
                        'execution_id' => $execution['insertExecution']['execution_id'],
                        'addtime' => time() ,
                        'hasvars' => $execution['insertExecution']['hasvars'] ,
                        'hisprocinst_id' => $execution['insertExecution']['hisprocinst_id'],
                        'stats' => '' ,
                        'model_name' => $execution['insertExecution']['model_name'] ,
                        'model_id' => $execution['insertExecution']['model_id'],
                    );
                }
            }
        }else{
            $needTask[] = array(
                'task_id' => $execution['insertExecution']['execution_id'] ,
                'activityname' => $execution['insertExecution']['activityname'] ,
                'assignee' => '',
                'execution_id' => $execution['insertExecution']['execution_id'],
                'addtime' => time() ,
                'hasvars' => $execution['insertExecution']['hasvars'] ,
                'hisprocinst_id' => $execution['insertExecution']['hisprocinst_id'],
                'stats' => '' ,
                'model_name' => $execution['insertExecution']['model_name'] ,
                'model_id' => $execution['insertExecution']['model_id'],
            );
        }
        return $needTask;
    }



}