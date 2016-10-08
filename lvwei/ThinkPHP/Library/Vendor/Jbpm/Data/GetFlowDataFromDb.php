<?php
namespace epet\hr\epetworkflow\data;
/**
 * wudan 吴丹 创建于 2016-09-30 10:46:26
从数据库中获得数据
 */
class GetFlowDataFromDb{

    /**
     * 模版数据
     */
    private $_deployData = null;

    /**
     * 临时局部参数
     */
    private $_variableList = null;

    /**
     * 获得整个数据
     */
    private $_allRelateDataDb = null;

    /**
     * 业务数库资料
     */
    private $_dbInfeList = null;


    /**
     * 初始化
     * @param int $varPdid executionId的id号
     * @param int $varModelId mode的id号
     */
    public function initi($varPdid , $varModelId){
        if($varPdid === 0){
            $this->getDeployDataFromDb($varModelId); //获得模版数据
        }else{
            $this->_queryALLRelateDataFromDb($varPdid);
        }
        $this->_setExecutionNumFromDb();
    }

    /**
     * 得到模版数据
     * @param int $varModelId 流程模版实例id
     * @return array
     */
    public function getDeployDataFromDb($varModelId){
        if(!$varModelId) return false;
        $model_id = intval($varModelId);
        $wdDbList = \EDB::table(\TAB_workflow_deployment::tableName())
            ->selectRaw("model_id , model_name , model_rule , model_stats")
            ->whereRaw("model_id = {$model_id}")->first();
        $wdDbList['businessTableName'] = $this->_dbInfeList['key'];
        $wdDbList['businessTableId'] = $this->_dbInfeList['id'];
        $this->_deployData = $wdDbList;
        return $wdDbList;
    }

    /**
     * 通过execution_id获得数据
     * @param int $varPdid 数据id
     */
    private function _queryALLRelateDataFromDb($varPdid){
        $allRelateDataDb = \EDB::table(\TAB_workflow_execution::tableName() . " AS we")
            ->join(\TAB_workflow_deployment::tableName() . " AS wd" , "we.model_id" , "=" , "wd.model_id")
            ->join(\TAB_workflow_hist_actinst::tableName() . " AS wha" , "we.execution_id" , "=" , "wha.execution_id")
            ->join(\TAB_workflow_hist_procinst::tableName() . " AS whp" , "we.hisprocinst_id" , "=" , "whp.hisprocinst_id")
            ->selectRaw("we.* , wd.model_id as wdmodel_id , wd.model_name as wdmodel_name , wd.model_rule,wd.model_stats , wha.* , whp.* , wha.addtime as whaaddtime , whp.addtime as whpaddtime ")
            ->whereRaw("we.execution_id = {$varPdid}")
            ->first();
        if(intval($allRelateDataDb['hasvars']) === 1){
            $variableList = \EDB::table(\TAB_workflow_variable::tableName())->whereRaw("execution_id = {$allRelateDataDb['execution_id']}")->get();
            $this->_variableList = $variableList;
        }
        $this->_deployData = array(
            'model_id' => $allRelateDataDb['wdmodel_id'],
            'model_name' => $allRelateDataDb['wdmodel_name'],
            'model_rule' => $allRelateDataDb['model_rule'],
            'model_stats' => $allRelateDataDb['model_stats'],
            'businessTableName' => $allRelateDataDb['key'],
            'businessTableId' => $allRelateDataDb['id']
        );
        $this->_allRelateDataDb = $allRelateDataDb;
    }

    /**
     * 设置数量
     */
    private function _setExecutionNumFromDb(){
        $numDbList = \EDB::table(\TAB_workflow_num::tableName())->first();
        $this->_deployData['execution_id'] = $numDbList['execution_num'] + 1;
        $this->_deployData['hisactinst_id'] = $this->_deployData['execution_id'];
        if($this->_deployData['businessTableName'] === 0){
            $this->_deployData['hisprocinst_id'] = $this->_deployData['execution_id'];
        }else{

        }
    }

    /**
     * 得到deployData
     */
    public function getDeployData(){
        return $this->_deployData;
    }

    /**
     * 得到局部局部变量
     */
    public function getVariableList(){
        return $this->_variableList;
    }

    /**
     * 得到整个数据
     */
    public function getAllRelateDataDb(){
        return $this->_allRelateDataDb;
    }

    /**
     * 设置dbinfo
     * @param array $varDbinfo 数据
     */
    public function setDbInfo($varDbinfo){
        $this->_dbInfeList = $varDbinfo;
    }
}