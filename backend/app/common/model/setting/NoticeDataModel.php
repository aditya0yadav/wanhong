<?php

namespace app\common\model\setting;

use think\Model;
use hg\apidoc\annotation as Apidoc;

/**
 * 通告消息模型
 */
class NoticeDataModel extends Model
{
    // 表名
    protected $name = 'notice_data';
    // 表主键
    protected $pk = 'notice_data_id';
}