<?php
namespace Vendor\Jbpm\Factory;
/**
 * wudan 吴丹 创建于 2016-09-30 10:29:38
执行方法工厂
 */
class ExecutionFactory{

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
     * 初始化
     * @param int $varPdid executionId的id号
     * @param int $varModelId mode的id号
     * @param array $varDbinfo 关联数据库信息
     */
    public function initi($varPdid , $varModelId = null , $varDbinfo = null){
        die(__FILE__);
    }


    /**
     * 把数据转换成Execution对象
     * @return 返回execution对象
     */
    public function buildExecution(){

    }
}