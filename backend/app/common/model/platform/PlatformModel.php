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
use app\common\model\file\FileModel;
use hg\apidoc\annotation as Apidoc;
use app\common\model\project\ProjectModel;
use app\common\model\platform\PlatformAuthModel;
use app\common\model\platform\CurrencyModel;
/**
 * 平台模型
 */
class PlatformModel extends Model
{
    // 表名
    protected $name = 'platform';
    // 表主键
    protected $pk = 'platform_id';
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
        return $this->hasOne(FileModel::class, 'file_id', 'platform_image')->append(['file_url'])->where(where_disdel());
    }
    // 关联项目
    public function projects()
    {
        return $this->hasMany(ProjectModel::class,'platform_id','platform_id')->where(where_disdel());
    }
    // 关联授权团队
    public function auths()
    {
        return $this->hasMany(PlatformAuthModel::class,'platform_id','platform_id');
    }
    // 关联货币
    public function currency()
    {
        return $this->hasOne(CurrencyModel::class, 'currency_id', 'platform_currency');
    }
    /**
     * 获取logo链接
     * @Apidoc\Field("")
     * @Apidoc\AddField("logo_url", type="string", desc="logo链接")
     */
    public function getLogoUrlAttr($value, $data)
    {
        return $this['logo']['file_url'] ?? '';
    }
}
