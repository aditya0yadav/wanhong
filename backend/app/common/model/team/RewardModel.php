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
use app\common\model\platform\PlatformModel;
use app\common\model\team\TeamModel;
use app\common\model\member\MemberModel;
use app\common\service\team\RewardService;
/**
 * 业绩模型
 */
class RewardModel extends Model
{
    // 表名
    protected $name = 'reward';
    // 表主键
    protected $pk = 'reward_id';
    // 关联平台
    public function platform()
    {
        return $this->hasOne(PlatformModel::class,'platform_id','platform_id');
    }
    // 关联团队
    public function team()
    {
        return $this->hasOne(TeamModel::class,'team_id','team_id');
    }
    // 关联用户
    public function member()
    {
        return $this->hasOne(MemberModel::class,'member_id','member_id');
    }
    public function getRewardStatusTextAttr($value, $data)
    {
        
        return RewardService::$rewardStatus[$data['reward_status']];
    }
}
