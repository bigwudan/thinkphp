<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/14
 * Time: 16:52
 */


/*

CREATE TABLE `think_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `think_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

CREATE TABLE `think_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE `think_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `think_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `logintime` int(10) unsigned NOT NULL,
  `loginip` varchar(30) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;




 */




namespace Vendor\Rbac;
use Vendor\Rbac\IRbac;

class MyRbac implements IRbac{

    /**
     * @param int $varAuthId 登录id
     * return boole
     */
    public static function saveAccessList($varAuthId){
        $authAccessList = self::_getAuthAccessListFromDB($varAuthId);
        $saveSessionFlag = session('authAccessList' , $authAccessList);
        return $saveSessionFlag ? $saveSessionFlag : null;
    }

    /**
     * 从access得到角色链
     * @return array
     */
    public static function getRecordAccessList(){
        $authAccessList = session('authAccessList');
        return $authAccessList ? $authAccessList : null;
    }

    /**
     * 从数据库得到权限数组
     */
    private static function _getAuthAccessListFromDB($varAuthId){
        if(null === $varAuthId) return false;
        $data = M('user')->alias('u')
            ->field(" u.username as username , ru.user_id , ru.role_id , r.name as role_name , a.node_id , n.name as node_name , n.title as node_title , n.pid , n.level")
            ->join('think_role_user AS ru ON ru.user_id = u.id')
            ->join('think_role AS r ON r.id = ru.role_id')
            ->join('think_access AS a ON a.role_id = r.id')
            ->join('think_node AS n ON a.node_id = n.id')
            ->where("ru.user_id = {$varAuthId}")->select();
        $sortAuthData = array();
        foreach($data as $k => $v)
            $sortAuthData[$v['level']][] = $v;
        $authAccessListTmp = array();
        foreach($sortAuthData[1] as $k => $v){
            foreach($sortAuthData[2] as $k1 => $v1)
                if($v['node_id'] == $v1['pid']) $authAccessListTmp[$v['node_name']][$v1['node_name']] = $v1;
        }
        $authAccessList = array();
        foreach($authAccessListTmp as $k => $v){
            foreach($v as $k1 => $v1){
                foreach($sortAuthData[3] as $k2 => $v2)
                    if($v1['node_id'] == $v2['pid']) $authAccessList[$k][$k1][$v2['node_name']] = true;
            }
        }
        return $authAccessList ? $authAccessList : null;
    }

    /**
     * 检查是否含有权限
     * @param string $varModuleName 模块名称
     * @param string $varModuleControllerName 模块名称
     * @param string $varActionName 模块名称
     */
    public static function checkAccess($varModuleName , $varModuleControllerName , $varActionName){
        $recordAccessList = self::getRecordAccessList();
        return $recordAccessList[$varModuleName][$varModuleControllerName][$varActionName] ? true : false;
    }

}