<?php
namespace epet\hr\epetworkflow\factory;
/**
 * wudan 吴丹 创建于 2016-09-30 11:31:32
XML引擎
 */
class XMLEngine{

    /**
     * xml对象
     */
    private $_xmlDocument = null;

    /**
     * 得到源目标数据
     */
    private $_sourceXmlList = null;

    /**
     * 获得xml函数
     * @param string $varXmlData Xml字符串
     * @param string $varActionName 当前action名字
     */
    public function initXml($varXmlData , $varActionName = null){
        $varActionName || $varActionName = 'start';
        $this->_setDbToXml($varXmlData);
        $this->_getActionXml($varActionName);
    }

    /**
     * 获得xml函数
     * @param string $varXmlData xml字符串
     * @return boole
     */
    private function _setDbToXml($varXmlData){
        $xmlDocument = new \DOMDocument();
        if($xmlDocument->loadXML($varXmlData)){
            $this->_xmlDocument = $xmlDocument;
            return true;
        }else{
            return false;
        }
    }

    /**
     * 判断当前对象
     * @param string $actionName 获得当前action对象
     */
    private function _getActionXml($actionName = null){
        if($actionName == 'start'){
            $actionNode = $this->_xmlDocument->getElementsByTagName('start')->item(0);
        }else{
            $xPathObj = new \DOMXPath($this->_xmlDocument);
            $actionNode = $xPathObj->query('*[@name="'.$actionName.'"]')->item(0);
        }
        $this->_dealSourceXml($actionNode);
    }

    /**
     * 得到源对象
     * @param xml $actionNode 节点对象
     */
    private function _dealSourceXml($actionNode){
        $nodeName = $actionNode->nodeName;
        $name = $actionNode->getAttribute('name');
        $attributeList = $this->_getAttributeList($actionNode);
        $transitionList = $this->_getTransitionList($actionNode);
        $this->_sourceXmlList = array(
            'nodeName' => $nodeName,
            'name' => $name,
            'attributeList' => $attributeList,
            'transitionList' => $transitionList
        );
        if($nodeName == 'decision'){
            if($actionNode->getElementsByTagName('handler')->length){
                $handerNode = $actionNode->getElementsByTagName('handler')->item(0);
                $this->_sourceXmlList['handler'] = trim($handerNode->getAttribute('class'));
            }
        }
    }

    /**
     * 得到属性数组
     * @param xmldocument $varXmlDocument 对象
     * @return array
     */
    private function _getAttributeList($varXmlDocument){
        $attributeList = array();
        $attributeXmlList = $varXmlDocument->attributes;
        if(!$attributeXmlList->length) return array();
        for($i = 0 ; $i < $attributeXmlList->length ; $i++){
            $tmpXml = $attributeXmlList->item($i);
            $attributeList[$i]['nodeName'] = $tmpXml->nodeName;
            $attributeList[$i]['nodeValue'] = $tmpXml->nodeValue;
            $attributeList[$i]['nodeType'] = $tmpXml->nodeType;
        }
        return $attributeList;
    }

    /**
     * 得到属性数组
     * @param xmlDocuement  $varXmlDocument 对象
     * @return array
     */
    private function _getTransitionList($varXmlDocument){
        $transitionXmlList = $varXmlDocument->getElementsByTagName('transition');
        $transitionList = array();
        if(!$transitionXmlList->length) return array();
        for($i = 0 ; $i < $transitionXmlList->length ; $i++){
            $tmpXml = $transitionXmlList->item($i);
            $transitionList[$i]['name'] = $tmpXml->getAttribute('name');
            $transitionList[$i]['to'] = $tmpXml->getAttribute('to');
        }
        return $transitionList;
    }

    /**
     * 得到SourceXml对象
     */
    public function getSourceXmlList(){
        return $this->_sourceXmlList;
    }

    /**
     * 所需找对象
     * @param string $varXmlData 字符串
     * @param string $varActionName 字符串
     * @return array
     */
    public function findActionNameToData($varXmlData , $varActionName){
        $xmlDocument = new \DOMDocument();
        $xmlDocument->loadXML($varXmlData);
        $xPathObj = new \DOMXPath($xmlDocument);
        $actionNode = $xPathObj->query('*[@name="' . $varActionName . '"]')->item(0);
        $nodeName = $actionNode->nodeName;
        $name = $actionNode->getAttribute('name');
        $attributeList = $this->_getAttributeList($actionNode);
        $transitionList = $this->_getTransitionList($actionNode);
        $targetXmList = array(
            'nodeName' => $nodeName,
            'name' => $name,
            'attributeList' => $attributeList,
            'transitionList' => $transitionList
        );
        if($actionNode->getElementsByTagName('handler')->length){
            $handerNode = $actionNode->getElementsByTagName('handler')->item(0);
            $targetXmList['handler'] = trim($handerNode->getAttribute('class'));
        }
        return $targetXmList;
    }


}