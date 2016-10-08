<?php
namespace epet\hr\epetworkflow\factory;
/**
 * wudan 吴丹 创建于 2016-09-30 14:26:29
ProcessEngine
 */
class ProcessEngine{
    /**
     * 得到ExecutionService
     */
    public function getExecutionService(){
        return new \epet\hr\epetworkflow\service\ExecutionService();
    }
}