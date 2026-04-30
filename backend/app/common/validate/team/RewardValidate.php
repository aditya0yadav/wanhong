<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\team;

use think\Validate;
use app\common\model\team\RewardModel;
use app\common\model\team\TeamModel;
use app\common\model\platform\PlatformModel;
use app\common\model\member\MemberModel;
/**
 * 团队验证器
 */
class RewardValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'reward_id'   => ['require'],
        'platform_id' => ['require','checkPlatformExisted'],
        'team_id' => ['require','checkTeamExisted'],
        'member_id' => ['require','checkMemberExisted']
    ];

    // 错误信息
    protected $message = [
    ];

    // 验证场景
    protected $scene = [
        'info'         => ['reward_id'],
        'add'          => ['platform_id','team_id','member_id'],
        'edit'         => ['reward_id','platform_id','team_id','member_id'],
        'dele'         => ['ids']
    ];
    // 自定义验证规则：团队是否已存在
    protected function checkTeamExisted($value, $rule, $data = [])
    {
        $model = new TeamModel();
        $where[] = ['team_id', '=', $data['team_id']];
        $where[] = ['is_disable', '=', 0];
        $info = $model->where($where)->find();
        if (empty($info)) {
            return '团队已禁用或不存在';
        }
        return true;
    }
    // 自定义验证规则：平台是否已存在
    protected function checkPlatformExisted($value, $rule, $data = [])
    {
        $model = new PlatformModel();
        $where[] = ['platform_id', '=', $data['platform_id']];
        $where[] = ['is_disable', '=', 0];
        $info = $model->where($where)->find();
        if (empty($info)) {
            return '平台已禁用或不存在';
        }
        return true;
    }
    // 自定义验证规则：用户是否已存在
    protected function checkMemberExisted($value, $rule, $data = [])
    {
        $model = new MemberModel();
        $where[] = ['member_id', '=', $data['member_id']];
        $where[] = ['is_disable', '=', 0];
        $where[] = ['is_delete', '=', 0];
        $info = $model->where($where)->find();
        if (empty($info)) {
            return '用户已禁用或不存在';
        }
        return true;
    }
}
