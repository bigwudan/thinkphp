<?php
namespace epet\hr\epetworkflow\data;
/**
 * wudan 吴丹 创建于 2016-09-30 13:53:27
execution对象
 */
class Execution{

    /**
     * 模版信息
     */
    private $_deployData = null;

    /**
     * 得到相关信息
     */
    private $_allRelateDataDb = null;

    /**
     * 得到参数
     */
    private $_varList = null;

    /**
     * 得到目标xml信息
     */
    private $_sourceXmlList = null;

    /**
     * 设置模版信息
     * @param array $varDeployData 模版参数
     */
    public function setDeployData($varDeployData){
        $this->_deployData = $varDeployData;
    }

    /**
     * 设置相关信息
     * @param array $varAllRelateDataDb 模版参数
     */
    public function setAllRelateDataDb($varAllRelateDataDb){
        $this->_allRelateDataDb = $varAllRelateDataDb;
    }

    /**
     * 参数
     * @param array $varVarList 参数
     */
    public function setVarList($varVarList){
        $this->_varList = $varVarList;
    }

    /**
     * 参数
     * @param array $varSourceXmlList 参数
     */
    public function setSourceXmlList($varSourceXmlList){
        $this->_sourceXmlList = $varSourceXmlList;
    }

    /**
     * 得到模版信息
     */
    public function getDeployData(){
        return $this->_deployData;
    }

    /**
     * 得到相关信息
     */
    public function getAllRelateDataDb(){
        return $this->_allRelateDataDb;
    }

    /**
     * 得到参数
     */
    public function getVarList(){
        return $this->_varList;
    }

    /**
     * 得到参数
     */
    public function getSourceXmlList(){
        return $this->_sourceXmlList;
    }



}