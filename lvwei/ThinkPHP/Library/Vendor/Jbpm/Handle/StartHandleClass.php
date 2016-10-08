<?php
namespace epet\hr\epetworkflow\handle;
/**
 * wudan 吴丹 创建于 2016-09-30 15:14:59
StartHandleClass
 */
class StartHandleClass extends \epet\hr\epetworkflow\handle\BaseHandleClass{
    /**
     * 参数
     */
    private $_histProcinst = null;

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

    /**
     * 设置HistProcinst
     */
    private function _setHistProcinst(){
        $deployData = $this->_executionObj->getDeployData();
        $allRelateDataDb = $this->_executionObj->getAllRelateDataDb();
        if(isset($allRelateDataDb['execution_id']) && $allRelateDataDb['execution_id']){
            $this->_histProcinst = null;
        }else{
            $insertHistProcinst = array(
                'hisprocinst_id' => $deployData['execution_id'] ,
                'model_id' =>$deployData['model_id'],
                'model_name' => $deployData['model_name'],
                'key' => $deployData['businessTableName'] ,
                'addtime' => time(),
                'endtime' => 0,
                'duration' => 0 ,
                'state' => 'active',
                'endactivity' => '' ,
                'id' => $deployData['businessTableId'],
            );
            $this->_histProcinst = array(
                'insert' => $insertHistProcinst,
                'updata' => 0
            );
        }
        return $this->_histProcinst;
    }

    /**
     * 覆盖父级 组合
     * @return 返回数据
     */
    public function combinTransationData(){
        $Transation = new \epet\hr\epetworkflow\data\Transation();
        $Transation->setExecution($this->_execution);
        $Transation->setHistActinst($this->_histActinst);
        $Transation->setVariable($this->_variable);
        $Transation->setHistTask($this->_histTask);
        $Transation->setTargetXmlAction($this->_targetXmlAction);
        $Transation->setHistProcinst($this->_setHistProcinst());
        $Transation->setNum($this->_execution['insertExecution']['execution_id']);
        $this->_transationObj = $Transation;
        return $Transation;
    }

}