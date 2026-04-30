<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\platform;

use think\Model;
use hg\apidoc\annotation as Apidoc;
use app\common\model\platform\MarkModel;
use app\common\model\member\MemberModel;
/**
 * 
 */
class MarkDetailModel extends Model
{
    // 表名
    protected $name = 'mark_detail';
    // 表主键
    protected $pk = 'mark_detail_id';
    public function mark()
    {
        return $this->hasOne(MarkModel::class, 'mark_id', 'mark_id');
    }
    // 关联用户
    public function member()
    {
        return $this->hasOne(MemberModel::class,'member_id','mark_user_id');
    }
}
