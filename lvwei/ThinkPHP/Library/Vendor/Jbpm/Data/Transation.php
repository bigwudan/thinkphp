<?php
namespace epet\hr\epetworkflow\data;
/**
 * wudan 吴丹 创建于 2016-09-30 17:45:35
Transation
 */
class Transation{

    /**
     * 得到
     */
    protected $_targetXmlAction = null;


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
    protected $_participation = null;

    /**
     * 得到数据
     */
    protected $_histProcinst = null;

    /**
     * 得到
     */
    protected $_task = null;

    /**
     * 得到num
     */
    protected $_num = null;

    /**
     * 设置
     * @param int $varNum 记数
     */
    public function setNum($varNum){
        $this->_num = $varNum;
    }

    /**
     * 设置
     * @param array $varHistProcinst 设置
     */
    public function setHistProcinst($varHistProcinst){
        $this->_histProcinst = $varHistProcinst;
    }

    /**
     * 设置
     * @param array $varTargetXmlAction 设置
     */
    public function setTargetXmlAction($varTargetXmlAction){
        $this->_targetXmlAction = $varTargetXmlAction;
    }

    /**
     * 设置
     * @param array $varExecution 设置
     */
    public function setExecution($varExecution){
        $this->_execution = $varExecution;
    }

    /**
     * 设置
     * @param array $varHistActinst 设置
     */
    public function setHistActinst($varHistActinst){
        $this->_histActinst = $varHistActinst;
    }

    /**
     * 设置
     * @param array $varVariable 设置
     */
    public function setVariable($varVariable){
        $this->_variable = $varVariable;
    }

    /**
     * 设置
     * @param array $varNeedHistTask 设置
     */
    public function setHistTask($varNeedHistTask){
        $this->_histTask = $varNeedHistTask;
    }

    /**
     * 设置
     * @param array $varParticipation 设置
     */
    public function setParticipation($varParticipation){
        $this->_participation = $varParticipation;
    }

    /**
     * 设置
     * @param array $varTask 设置
     */
    public function setTask($varTask){
        $this->_task = $varTask;
    }

    /**
     * 设置
     */
    public function getExecution(){
        return $this->_execution;
    }

    /**
     * 设置
     */
    public function getHistActinst(){
        return $this->_histActinst;
    }

    /**
     * 设置
     */
    public function getVariable(){
        return $this->_variable;
    }

    /**
     * 设置
     */
    public function getHistTask(){
        return $this->_histTask;
    }

    /**
     * 设置
     */
    public function getParticipation(){
        return $this->_participation;
    }

    /**
     * 设置
     */
    public function getTask(){
        return $this->_task;
    }

    /**
     * 得到
     */
    public function getTargetXmlAction(){
        return $this->_targetXmlAction;
    }

    /**
     * 设置
     */
    public function getHistProcinst(){
        return $this->_histProcinst;
    }

    /**
     * 得到
     */
    public function getNum(){
        return $this->_num;
    }

}