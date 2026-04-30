<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\platform;

use think\Validate;
use app\common\model\platform\PlatformModel;
use app\common\model\team\TeamModel;
use app\common\model\platform\PlatformAuthModel;
/**
 * 平台授权验证器
 */
class PlatformAuthValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'platform_id'   => ['require','checkExisted'],
        'team_id' => ['require','checkTeamExisted'],
        'platform_auth_id' =>['require']
    ];

    // 错误信息
    protected $message = [
        'platform_id.require' => '平台ID不能为空',
        'team_id.require' => '团队ID不能为空',
        'platform_sign.alphaNum' => '平台标识只能为数字与字母',
        'platform_auth_id.require' => '授权ID不能为空'
    ];

    // 验证场景
    protected $scene = [
        'add'          => ['platform_id','team_id'],
        'edit'          => ['platform_id'],
        'dele'         => ['platform_auth_id'],
    ];

    // 自定义验证规则：检测平台是否开放
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new PlatformModel();
        $where[] = ['platform_id', '=', $value];
        $where[] = ['is_disable','=',0];
        $info = $model->where($where)->find();
        if (!$info) {
            return '平台不存在或未开放';
        }
        return true;
    }
    // 自定义验证规则：团队检测
    protected function checkTeamExisted($value, $rule, $data = [])
    {
        $model = new TeamModel();
        $where[] = ['team_id', '=', $value];
        $info = $model->where($where)->find();
        if (!$info) {
            return '团队不存在';
        }
        $authModel = new PlatformAuthModel();
        $where[] = ['platform_id','=',$data['platform_id']];
        $authInfo = $authModel->where($where)->find();
        if($authInfo){
            return '已授权该平台';
        }
        return true;
    }
}
