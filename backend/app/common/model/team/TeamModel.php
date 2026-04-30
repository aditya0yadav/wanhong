<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\team;

use think\Model;
use hg\apidoc\annotation as Apidoc;

use app\common\model\file\FileModel;
/**
 * 团队模型
 */
class TeamModel extends Model
{
    // 表名
    protected $name = 'team';
    // 表主键
    protected $pk = 'team_id';
    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? '是' : '否';
    }
    // 关联Logo
    public function logo()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'team_logo')->append(['file_url'])->where(where_disdel());
    }
    public function getLogoUrlAttr($value, $data)
    {
        return $this['logo']['file_url'] ?? '';
    }
}
