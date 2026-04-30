<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\model\project;

use think\Model;
use app\common\model\file\FileModel;
use app\common\model\platform\CurrencyModel;
use app\common\model\platform\PlatformModel;
use app\common\model\team\RewardModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 项目模型
 */
class ProjectModel extends Model
{
    // 表名
    protected $name = 'project';
    // 表主键
    protected $pk = 'project_id';
    /**
     * 获取是否禁用名称
     * @Apidoc\Field("")
     * @Apidoc\AddField("is_disable_name", type="string", desc="是否禁用名称")
     */
    public function getIsDisableNameAttr($value, $data)
    {
        return ($data['is_disable'] ?? 0) ? '是' : '否';
    }
    // 关联文档
    public function projectFile()
    {
        return $this->hasOne(FileModel::class, 'file_id', 'project_file_id')->append(['file_url'])->where(where_disdel());
    }
    // 关联货币
    public function currency()
    {
        return $this->hasOne(CurrencyModel::class, 'currency_id', 'project_currency');
    }
    // 关联平台
    public function platform()
    {
        return $this->hasOne(PlatformModel::class, 'platform_id', 'platform_id');
    }
    // 关联项目
    public function rewards()
    {
        return $this->hasMany(RewardModel::class,'project_pno','project_pno');
    }
    /**
     * 获取文档链接
     */
    public function getProjectFileUrlAttr($value, $data)
    {
        return $this['projectFile']['file_url'] ?? '';
    }
}
