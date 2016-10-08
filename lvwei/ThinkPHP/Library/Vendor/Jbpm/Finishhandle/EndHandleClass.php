<?php
namespace epet\hr\epetworkflow\finishhandle;
/**
 * wudan 吴丹 创建于 2016-10-01 17:33:25
结束
 */
class EndHandleClass{
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
        $histTask = $this->_transationObj->getHistTask();
        $execution['insertExecution'] = null;
        $histActinst['insert'] = null;
        $variable = null;
        $histProcinst = array(
            'hisprocinst_id' => $varExecutionObj->getAllRelateDataDb()['hisprocinst_id'],
            'updata'=>array(
                'endtime' => time(),
                'duration' => time() - $varExecutionObj->getAllRelateDataDb()['whpaddtime'],
                'state' => 'ended',
                'endactivity' => $this->_transationObj->getTargetXmlAction()['name']
            )
        );
        $num = null;
        $participation = null;
        $task = null;
        $this->_transationObj->setNum($num);
        $this->_transationObj->setHistProcinst($histProcinst);
        $this->_transationObj->setExecution($execution);
        $this->_transationObj->setHistActinst($histActinst);
        $this->_transationObj->setVariable($variable);
        $this->_transationObj->setHistTask($histTask);
        $this->_transationObj->setParticipation($participation);
        $this->_transationObj->setTask($task);
        return $this->_transationObj;
    }
}