<?php
namespace epet\hr\epetworkflow\process;
/**
 * wudan 吴丹 创建于 2016-10-01 11:15:16
写入数据库
 */
class WriteToDb{
    /**
     * TransationObj对象
     */
    private $_transationObj = null;

    /**
     *  初始化
     * @param obj $varTransationObj 需要转换对象
     */
    public function initi($varTransationObj){
        $this->_transationObj = $varTransationObj;
    }

    /**
     * 执行sql
     * @return int execution
     */
    public function exec(){
        \DB::beginTransaction();
        $execution = $this->_transationObj->getExecution();
        $histActinst = $this->_transationObj->getHistActinst();
        $variable = $this->_transationObj->getVariable();
        $histProcinst = $this->_transationObj->getHistProcinst();
        $num = $this->_transationObj->getNum();
        $histTask = $this->_transationObj->getHistTask();
        $participation = $this->_transationObj->getParticipation();
        $task = $this->_transationObj->getTask();
        try{
            if($execution['preHisactinst_id']){
                \EDB::table(\TAB_workflow_execution::tableName())->whereRaw("execution_id = {$execution['preHisactinst_id']}")->take(1)->delete();
            }
            if($execution['insertExecution']){
                \EDB::table(\TAB_workflow_execution::tableName())->insert($execution['insertExecution']);
            }
            if($histActinst['updata']){
                \EDB::table(\TAB_workflow_hist_actinst::tableName())->take(1)->whereRaw("hisactinst_id = {$histActinst['updata']['preHisactinst_id']}")->update($histActinst['updata']['upData']);
            }
            if($histActinst['insert']){
                \EDB::table(\TAB_workflow_hist_actinst::tableName())->insert($histActinst['insert']);
            }
            \EDB::table(\TAB_workflow_variable::tableName())->whereRaw("execution_id = {$execution['preHisactinst_id']}")->delete();
            if($variable['variableList']){
                \EDB::table(\TAB_workflow_variable::tableName())->insert($variable['variableList']);
            }
            if($histProcinst){
                if(isset($histProcinst['insert']) && $histProcinst['insert']){
                    \EDB::table(\TAB_workflow_hist_procinst::tableName())->insert($histProcinst['insert']);
                }else{
                    \EDB::table(\TAB_workflow_hist_procinst::tableName())->whereRaw("hisprocinst_id = {$histProcinst['hisprocinst_id']}")->update($histProcinst['updata']);
                }
            }
            if($histTask){
                if(isset($histTask['upData']) && $histTask['upData']){
                    \EDB::table(\TAB_workflow_hist_task::tableName())->take(1)->whereRaw("execution_id = {$execution['preHisactinst_id']}")->update($histTask['upData']);
                }else{
                    \EDB::table(\TAB_workflow_hist_task::tableName())->insert($histTask['insert']);
                }
            }
            \EDB::table(\TAB_workflow_participation::tableName())->take(1)->whereRaw("task_id = {$execution['preHisactinst_id']}")->delete();
            if($participation){
                \EDB::table(\TAB_workflow_participation::tableName())->insert($participation);
            }
            \EDB::table(\TAB_workflow_task::tableName())->whereRaw("execution_id = {$execution['preHisactinst_id']}")->delete();
            if($task){
                \EDB::table(\TAB_workflow_task::tableName())->insert($task);
            }
            if($num) \EDB::table(\TAB_workflow_num::tableName())->take(1)->update(array('execution_num' => $num));
            \DB::commit();
            $result = true;
        }catch (\RUNError $e){
            \DB::rollBack($e);
            $result = false;;
        }
        return $result;
    }


}