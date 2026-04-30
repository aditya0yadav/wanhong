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
use app\common\model\platform\PlatformModel;
use app\common\model\team\TeamModel;
/**
 * 平台授权模型
 */
class PlatformAuthModel extends Model
{
    // 表名
    protected $name = 'platform_auth';
    // 表主键
    protected $pk = 'platform_auth_id';
    // 关联平台
    public function platform()
    {
        return $this->hasOne(PlatformModel::class, 'platform_id', 'platform_id')->append(['platform_name','platform_sign']);
    }
    // 关联团队
    public function team()
    {
        return $this->hasOne(TeamModel::class, 'team_id', 'team_id')->append(['team_name','team_host','auth_num'])->where(where_disdel());
    }
    public function getPlatformNameAttr($value, $data)
    {
        return $this['platform']['platform_name'] ?? '';
    }
    public function getPlatformSignAttr($value, $data)
    {
        return $this['platform']['platform_sign'] ?? '';
    }
    public function getTeamNameAttr($value, $data)
    {
        return $this['team']['team_name'] ?? '';
    }
    public function getTeamHostAttr($value, $data)
    {
        return $this['team']['team_host'] ?? '';
    }
    public function getAuthNumAttr($value, $data)
    {
        return $this['team']['auth_num'] ?? '';
    }
}
