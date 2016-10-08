<?php
namespace epet\hr\epetworkflow\handle;
/**
 * wudan 吴丹 创建于 2016-09-30 15:11:45
BaseHandleClass
 */
class BaseHandleClass{

    /**
     * 数据
     */
    protected $_executionObj = null;

    /**
     * targetXmlAction
     */
    protected $_targetXmlAction = null;

    /**
     * 需要设置的参数
     */
    protected $_needSetvarList = null;

    /**
     * 需要设置的参数
     */
    protected $_transition = null;

    /**
     * 得到
     */
    protected $_execution = null;

    /**
     * 得到历史数据
     */
    protected $_histActinst = null;

    /**
     * 得到数据
     */
    protected $_variable = null;

    /**
     * 得到数据
     */
    protected $_histTask = null;

    /**
     * 得到数据
     */
    protected $_transationObj = null;

    /**
     * 初始化
     * @param array $varExecutionObj 类
     * @param string $varTransition 需要调整参数
     * @param array $varNeedSetVarList 需要设置变量
     */
    public function initi($varExecutionObj , $varTransition = null , $varNeedSetVarList){
        $this->_executionObj = $varExecutionObj;
        $this->_needSetvarList = $varNeedSetVarList;
        $this->_transition = $varTransition;
        $this->_setTargetXmlAction();
        $this->_setExecution();
        $this->_setHistActinst();
        if($this->_executionObj->getSourceXmlList()['nodeName'] === 'task'){
            $this->_histTask();

        }
        $varNeedSetVarList && $this->_setVariable();
    }

    /**
     * 得到needExecution
     */
    private function _setExecution(){
        $hasVars = $this->_needSetvarList ? 1 : 0;
        $deployData = $this->_executionObj->getDeployData();
        $allRelateDataDb = $this->_executionObj->getAllRelateDataDb();
        $allRelateDataDb['hisprocinst_id'] || $allRelateDataDb['hisprocinst_id'] = $deployData['execution_id'];
        $insertExecutionList = array(
            'execution_id' => $deployData['execution_id'],
            'model_name' =>$deployData['model_name'],
            'activityname' =>$this->_targetXmlAction['name'],
            'key' => $deployData['businessTableName'],
            'hasvars'  => $hasVars,
            'hisactinst_id' =>$deployData['execution_id'],
            'model_id' => $deployData['model_id'],
            'hisprocinst_id' =>$allRelateDataDb['hisprocinst_id'],
            'id' => $deployData['businessTableId'],
            'state' => 'active-root',
            'addtime' => time()
        );
        if(!isset($allRelateDataDb['execution_id'])){
            $allRelateDataDb['execution_id'] = 0;
        }
        //需要处理execution表中
        $needExecution = array(
            'preHisactinst_id' => $allRelateDataDb['execution_id'],
            'insertExecution' => $insertExecutionList
        );
        $this->_execution = $needExecution;
    }

    /**
     * 得到HistActinst
     */
    private function _setHistActinst(){
        $deployData = $this->_executionObj->getDeployData();
        $allRelateDataDb = $this->_executionObj->getAllRelateDataDb();
        $upDataHistActinstDbList = 0;
        if($allRelateDataDb){
            $upDataHistActinstDbList = array(
                'preHisactinst_id' => $allRelateDataDb['execution_id'],
                'upData' => array(
                    'endtime' => time(),
                    'duration' => time() - $allRelateDataDb['whaaddtime'],
                    'transition' => "to {$this->_targetXmlAction['name']}",
                )
            );
        }
        $allRelateDataDb['hisprocinst_id'] || $allRelateDataDb['hisprocinst_id'] = $deployData['execution_id'];
        $insertHistActinstDbList = array(
            'hisactinst_id' => $deployData['execution_id'],
            'model_name'=>$deployData['model_name'],
            'model_id' => $deployData['model_id'],
            'key' => $deployData['businessTableName'],
            'state' => 'active-root',
            'type' => $this->_targetXmlAction['nodeName'],
            'activityname' => $this->_targetXmlAction['name'],
            'transition' => '',
            'addtime' => time(),
            'endtime' => 0,
            'duration' => 0,
            'hisprocinst_id' => $allRelateDataDb['hisprocinst_id'],
            'histask_id' =>0,
            'classname' => '',
            'execution_id' => $deployData['execution_id'],
        );

        //需要处理HistActinst 表中
        $needHistActinst = array(
            'updata' => $upDataHistActinstDbList,
            'insert' => $insertHistActinstDbList
        );
        $this->_histActinst = $needHistActinst;
    }

    /**
     * 设置varList
     */
    private function _setVariable(){
        $varsList = null;
        $deployData = $this->_executionObj->getDeployData();
        $allRelateDataDb = $this->_executionObj->getAllRelateDataDb();
        foreach($this->_needSetvarList as $k => $v){
            $this->_needSetvarList[$k]['execution_id'] =  $deployData['execution_id'];
        }
        if($this->_targetXmlAction['nodeName'] == 'task'){
            $varsList = $this->_constructVarsList($this->_needSetvarList,$deployData['execution_id']);
        }else{
            $varsList = $this->_constructVarsList($this->_needSetvarList);
        }
        $needVariable = array(
            'preHisactinst_id' => $allRelateDataDb['execution_id'],
            'variableList' => $varsList
        );
        $this->_variable = $needVariable;

    }

    /**
     * 静态公共方法组合数组
     * @param array $varVarsList 组合insert临时参数
     * @param int $varTaskId 组合insert临时参数
     * @return array
     */
    private function _constructVarsList($varVarsList , $varTaskId = 0){
        $modelList = array(
            'execution_id'=>0,
            'class'=>'',
            'task_id' => 0,
            'key' => '',
            'string_value' => '',
            'int_value' => 0,
            'double_value' => 0
        );
        $insertVarsList = array();
        foreach($varVarsList as $k => $v){
            $tmp = $modelList;
            $tmp['execution_id'] = $v['execution_id'];
            $tmp['task_id'] = $varTaskId;
            $tmp['key'] = $v['key'];
            $tmp[$v['type']."_value"] = $v['value'];
            $tmp['class'] = str_replace('_value','',$v['type']);
            $insertVarsList[] = $tmp;
        }
        return $insertVarsList;
    }


    /**
     * 设置task
     */
    private function _histTask(){
        $allRelateDataDb = $this->_executionObj->getAllRelateDataDb();
        //需要处理HistTask
        $needHistTask = array(
            'histTaskId' => $allRelateDataDb['execution_id'],
            'upData' => array(
                'endtime' => time(),
                'duration' => time() - $allRelateDataDb['whaaddtime'],
                'stats' => 'completed',
                'outcome' => "to {$this->_targetXmlAction['name']}"
            )
        );
        $this->_histTask = $needHistTask;
    }


    /**
     * 组合
     * @return 返回数据
     */
    public function combinTransationData(){
        $Transation = new \epet\hr\epetworkflow\data\Transation();
        $Transation->setExecution($this->_execution);
        $Transation->setHistActinst($this->_histActinst);
        $Transation->setVariable($this->_variable);
        $Transation->setHistTask($this->_histTask);
        $Transation->setTargetXmlAction($this->_targetXmlAction);
        $Transation->setNum($this->_execution['insertExecution']['execution_id']);
        $this->_transationObj = $Transation;
        return $Transation;
    }

    /**
     * 得到transation对象
     */
    public function getTransationObj(){
        return $this->_transationObj;
    }
}