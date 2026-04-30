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

/**
 * 平台验证器
 */
class PlatformValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'platform_id'   => ['require'],
        'platform_name' => ['require'],
        'platform_currency' =>['require'],
        'platform_sign' => ['require','checkSignExisted'],
        'params' => ['array','checkParams'],
        'project_params' => ['array','checkProjectParams']
    ];

    // 错误信息
    protected $message = [
        'platform_name.require' => '请输入平台名称',
        'platform_sign.require' => '请输入平台标识',
        'platform_sign.alphaNum' => '平台标识只能为数字与字母'
    ];

    // 验证场景
    protected $scene = [
        'info'         => ['platform_id'],
        'add'          => ['platform_name','platform_sign','params','project_params','platform_currency'],
        'edit'         => ['platform_id','platform_name','platform_sign','params','project_params','platform_currency'],
        'dele'         => ['ids'],
        'disable'      => ['ids'],
    ];
    protected function checkParams($value, $rule, $data = []){
        $expectedKeys = ['key','name','value'];
        foreach ($value as $subArray) {
            if (!is_array($subArray) || !empty(array_diff(array_keys($subArray), $expectedKeys)) || !empty(array_diff_key(array_flip($expectedKeys), $subArray))) {
                return '对接参数格式错误';
            }
            if(count($value) > 1 && (!$subArray['name'] || !$subArray['value'])){
                return '请完善对接参数信息';
            }
            if(count($value) == 1 && (($subArray['name'] && !$subArray['value']) || (!$subArray['name'] && $subArray['value']))){
                return '请完善对接参数信息';
            }
        }
        $names = array_column($value, 'name');
        if(count($names) !== count(array_unique($names))){
            return '对接参数不可重复';
        }
        return true;
    }
    protected function checkProjectParams($value, $rule, $data = []){
        $expectedKeys = ['key','name','field','value'];
        foreach ($value as $subArray) {
            if (!is_array($subArray) || !empty(array_diff(array_keys($subArray), $expectedKeys)) || !empty(array_diff_key(array_flip($expectedKeys), $subArray))) {
                return '项目参数格式错误';
            }
        }
        $names = array_column($value, 'name');
        if(count($names) !== count(array_unique($names))){
            return '项目参数名重复';
        }
        $fields = array_column($value, 'field');
        if(count($fields) !== count(array_unique($fields))){
            return '项目参数标识重复';
        }
        return true;
    }
    // 自定义验证规则：平台标识是否已存在
    protected function checkSignExisted($value, $rule, $data = [])
    {
        $model = new PlatformModel();
        $pk = $model->getPk();
        $id = $data[$pk] ?? 0;

        $where[] = [$pk, '<>', $id];
        $where[] = ['platform_sign', '=', $data['platform_sign']];
        $info = $model->field($pk)->where($where)->find();
        if ($info) {
            return '平台标识已存在：' . $data['platform_sign'];
        }
        return true;
    }
}
