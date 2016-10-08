<?php
namespace epet\hr\epetworkflow\handle;
/**
 * wudan 吴丹 创建于 2016-09-30 16:58:19
DecisionHandleClass
 */
class DecisionHandleClass extends \epet\hr\epetworkflow\handle\BaseHandleClass{
    /**
     * 选择
     */
    protected function _setTargetXmlAction(){
        $transitionList = null;
        $sourceXml = $this->_executionObj->getSourceXmlList();
        $deployData = $this->_executionObj->getDeployData();
        if($sourceXml['handler']){
            $className = $sourceXml['handler'];
            $testClass = new $className();
            $toName = $testClass->decide($this->_executionObj);
        }
        foreach($sourceXml['transitionList']  as $k => $v){
            if($v['name'] === $toName){
                $transitionList = $v['to'];
                break;
            }
        }
        $XmlEngin = new \epet\hr\epetworkflow\factory\XMLEngine();
        $this->_targetXmlAction = $XmlEngin->findActionNameToData($deployData['model_rule'] , $transitionList);
    }
}