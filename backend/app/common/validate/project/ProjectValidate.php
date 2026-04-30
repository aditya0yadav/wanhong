<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\project;

use think\Validate;
use app\common\model\project\ProjectModel;

/**
 * 项目验证器
 */
class ProjectValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'project_id'   => ['require'],
        'platform_id' => ['require'],
        'project_pno' => ['require'],
        'project_no' => ['require'],
        'project_name' => ['require'],
        'project_currency' => ['require'],
        'project_sign' => ['require','checkExisted']
    ];

    // 错误信息
    protected $message = [
        'project_no.require' => '请输入项目ID',
        'platform_name.require' => '请输入项目名称',
        'platform_id.require' => '请输入平台ID',
        'project_currency.require' => '请选择币种'
    ];

    // 验证场景
    protected $scene = [
        'info'         => ['project_id'],
        'add'          => ['project_pno','project_no','project_name','platform_id','project_currency','project_sign'],
        'edit'         => ['project_id','project_no','project_name','platform_id','project_currency'],
        'dele'         => ['ids'],
        'disable'      => ['ids'],
    ];
    protected function sceneEdit()
    {
        return $this->only(['project_id','project_no','project_name','platform_id','project_currency'])
            ->append('project_sign', ['checkExisted']);
    }
    // 自定义验证规则：检测平台是否开放
    protected function checkExisted($value, $rule, $data = [])
    {
        $model = new ProjectModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;
        $where[] = [$pk, '<>', $id];
        $where[] = ['project_sign', '=', $value];
        $info = $model->where($where)->find();
        if ($info) {
            return '存在项目PID【'.$info['project_pno'].'】与当前编辑信息一致,操作失败';
        }
        return true;
    }
}
