<?php
namespace epet\hr\epetworkflow\handle;
/**
 * wudan 吴丹 创建于 2016-10-01 17:09:19
CommonHandleClass
 */
class CommonHandleClass extends \epet\hr\epetworkflow\handle\BaseHandleClass {
    /**
     * 选择
     */
    protected function _setTargetXmlAction(){
        $transitionList = null;
        $sourceXml = $this->_executionObj->getSourceXmlList();
        $deployData = $this->_executionObj->getDeployData();
        if($this->_transition){
            foreach($sourceXml['transitionList'] as $k => $v){
                if($v['name'] === $this->_transition){
                    $transitionList = $v['to'];
                    break;
                }
            }
        }else{
            $transitionList = $sourceXml['transitionList'][0]['to'];
        }
        $XmlEngin = new \epet\hr\epetworkflow\factory\XMLEngine();
        $this->_targetXmlAction = $XmlEngin->findActionNameToData($deployData['model_rule'] , $transitionList);
    }
}