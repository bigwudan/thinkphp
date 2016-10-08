<?php
namespace Vendor\Jbpm\Service;
/**
 * wudan 吴丹 创建于 2016-09-30 14:29:07
ExecutionService
 */
class ExecutionService{

    /**
     * Execution对象
     */
    private $_executionObj = null;

    /**
     * 需要设置参数
     */
    private $_needSetVarList = null;

    /**
     * 制度跳转
     */
    private $_transition = null;

    /**
     * 开始一个实例
     * @param int $varModelId 模版id jz_workflow_deployment
     * @param array $varDbinfo 数据库信息
     * @param array $varVarsList 临时变量
     * @return boole
     */
    public function startProcessInstanceByKey($varModelId , $varDbinfo , $varVarsList = array()){

        $ExecutionFactory = new \Vendor\Jbpm\Factory\ExecutionFactory();
        $ExecutionFactory->initi(1,2,3);
//        $ExecutionFactory->initi(0,$varModelId , $varDbinfo);
//        $this->_executionObj = $ExecutionFactory->buildExecution();
//        $varVarsList && $this->_needSetVarList = $varVarsList;
//        $this->_choiceHandle();
    }

    /**
     * 跳转到下一个流程
     * @param int $varPiid 模版id jz_workflow_deployment
     * @param string $varTransition transition值跳转值
     * @param array $varVarsList 临时变量
     */
    public function signalExecutionById($varPiid, $varTransition = null , $varVarsList = array()){
        $ExecutionFactory = new \epet\hr\epetworkflow\factory\ExecutionFactory();
        $ExecutionFactory->initi($varPiid);
        $this->_executionObj = $ExecutionFactory->buildExecution();
        $this->_transition = $varTransition;
        $varVarsList && $this->_needSetVarList = $varVarsList;
        $this->_choiceHandle();
    }

    /**
     * 获取xml
     */
    private function _choiceHandle(){
        $result = false;
        switch($this->_executionObj->getSourceXmlList()['nodeName']){
            case 'start':
                $handleClass = new \epet\hr\epetworkflow\handle\StartHandleClass();
                $handleClass->initi($this->_executionObj , $this->_transition , $this->_needSetVarList);
                $this->_transition = $handleClass->combinTransationData();
                $this->_choiceFinishHandle();
                $WriteToDb = new \epet\hr\epetworkflow\process\WriteToDb();
                $WriteToDb->initi($this->_transition);
                $result = $WriteToDb->exec();
                break;
            case 'decision':
                $handleClass = new \epet\hr\epetworkflow\handle\DecisionHandleClass();
                $handleClass->initi($this->_executionObj , $this->_transition , $this->_needSetVarList);
                $this->_transition = $handleClass->combinTransationData();
                $this->_choiceFinishHandle();
                $WriteToDb = new \epet\hr\epetworkflow\process\WriteToDb();
                $WriteToDb->initi($this->_transition);
                $result = $WriteToDb->exec();
                break;
            case 'state':
                $handleClass = new \epet\hr\epetworkflow\handle\CommonHandleClass();
                $handleClass->initi($this->_executionObj , $this->_transition , $this->_needSetVarList);
                $this->_transition = $handleClass->combinTransationData();
                $this->_choiceFinishHandle();
                $WriteToDb = new \epet\hr\epetworkflow\process\WriteToDb();
                $WriteToDb->initi($this->_transition);
                $result = $WriteToDb->exec();
                break;
            case 'task':
                $handleClass = new \epet\hr\epetworkflow\handle\CommonHandleClass();
                $handleClass->initi($this->_executionObj , $this->_transition , $this->_needSetVarList);
                $this->_transition = $handleClass->combinTransationData();
                $this->_choiceFinishHandle();
                $WriteToDb = new \epet\hr\epetworkflow\process\WriteToDb();
                $WriteToDb->initi($this->_transition);
                $result = $WriteToDb->exec();
                break;
            default:
                break;
        }
        return $result;
    }

    /**
     * 策约选择
     */
    private function _choiceFinishHandle(){
        switch($this->_transition->getTargetXmlAction()['nodeName']){
            case 'start':
                break;
            case 'decision':
                break;
            case 'state':
                break;
            case 'task':
                $TaskHandleClassObj = new \epet\hr\epetworkflow\finishhandle\TaskHandleClass();
                $this->_transition = $TaskHandleClassObj->initi($this->_transition , $this->_executionObj);
                break;
            case 'end':
                $TaskHandleClassObj = new \epet\hr\epetworkflow\finishhandle\EndHandleClass();
                $this->_transition = $TaskHandleClassObj->initi($this->_transition , $this->_executionObj);
                break;
            default:
                break;
        }
    }

    /**
     * 得到transation
     */
    public function getTransition(){
        return $this->_transition;
    }
}