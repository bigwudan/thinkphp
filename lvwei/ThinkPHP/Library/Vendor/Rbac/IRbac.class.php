<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/14
 * Time: 16:52
 */

namespace Vendor\Rbac;


interface IRbac
{
    /**
     * @param $authId
     * @param $data
     */
    public static function saveAccessList($varAuthId);

    /**
     * @param $template
     * @return mixed
     */
    public static function getRecordAccessList();


}