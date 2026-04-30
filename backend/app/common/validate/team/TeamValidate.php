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
use app\common\model\team\TeamModel;
use app\common\model\team\AttributesModel;
use app\common\model\team\TeamApisModel;

/**
 * 团队验证器
 */
class TeamValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'team_id'   => ['require'],
        'team_name' => ['require', 'checkExisted'],
        'team_host' => ['require','checkHostExisted','isValidDomain'],
        'auth_num' =>['require', 'between' => '0,1000'],
        'commission_ratio'=>['require', 'between' => '0,100']
    ];

    // 错误信息
    protected $message = [
        'team_name.require' => '请输入名称',
        'team_host.require' => '请输入团队域名',
        'auth_num.require'  => '请输入授权用户数量',
        'auth_num.between'  => '用户数量限制0-1000',
        'commission_ratio.require'  => '请输入抽佣比例',
        'commission_ratio.between'  => '抽佣比例限制0-100',
    ];

    // 验证场景
    protected $scene = [
        'info'         => ['team_id'],
        'add'          => ['team_name','team_host','auth_num','commission_ratio'],
        'edit'         => ['team_id', 'team_name','team_host','auth_num','commission_ratio'],
        'dele'         => ['ids'],
        'disable'      => ['ids'],
    ];
    protected function isValidDomain($value, $rule, $data = []) {
            $domainRegex = "/^([a-z0-9][a-z0-9\-]{1,66}\.)+[a-z0-9][a-z0-9\-]{1,66}$/i";
            if(!preg_match($domainRegex, $value) && $value !='localhost'){
                return '域名不符合要求';
            };
            return true;
    }
    // 自定义验证规则：团队是否已存在
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new TeamModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['team_name', '=', $data['team_name']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '团队名称已存在：' . $data['team_name'];
        }
        return true;
    }
    // 自定义验证规则：团队域名是否已存在
    protected function checkHostExisted($value, $rule, $data = [])
    {
        $model = new TeamModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['team_host', '=', $data['team_host']];
        $where = where_delete($where);
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '团队名称已存在：' . $data['team_host'];
        }
        return true;
    }
}
