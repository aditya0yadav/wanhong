<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\validate\setting;

use think\Validate;

/**
 * 通告管理验证器
 */
class NoticeValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'ids'        => ['require', 'array'],
        'notice_id'  => ['require'],
        'type'       => ['require'],
        'title'      => ['require'],
        'content'  => ['require'],
    ];

    // 错误信息
    protected $message = [
        'title.require'      => '请输入标题',
        'type.require'       => '请选择类型',
        'content.require' => '请输入通知内容',
    ];

    // 验证场景
    protected $scene = [
        'info'     => ['notice_id'],
        'add'      => ['type', 'title', 'content'],
        'edit'     => ['notice_id', 'type', 'title', 'start_time', 'end_time'],
        'dele'     => ['ids'],
        'edittype' => ['ids', 'type'],
        'datetime' => ['ids', 'start_time', 'end_time'],
        'disable'  => ['ids'],
    ];
}
