<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\topic;

use think\Model;
use hg\apidoc\annotation as Apidoc;
/**
 * 人设答案模型
 */
class TopicAnserModel extends Model
{
    // 表名
    protected $name = 'topic_anser';
    // 表主键
    protected $pk = 'topic_anser_id';

}
