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
use app\common\model\platform\PlatformModel;
/**
 * 人设模型
 */
class TopicModel extends Model
{
    // 表名
    protected $name = 'topic';
    // 表主键
    protected $pk = 'topic_id';
    public function platform()
    {
        return $this->hasOne(PlatformModel::class, 'platform_id', 'platform_id')->where(where_disdel());
    }
}
