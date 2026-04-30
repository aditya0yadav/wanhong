<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\platform;
use app\common\model\platform\PlatformAuthModel;

/**
 * 平台授权
 */
class PlatformAuthService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'platform_auth_id/d'   => '',
        'platform_id/s'   => '',
        'team_id/s' => '',
        'auth_rate/d' => 0
    ];
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new PlatformAuthModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',platform_id,team_id,auth_rate';
        } else {
            $field = $group . ',' . $field;
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $model = $model->alias('a');
        $where = array_values($where);
        $count = $pages = 0;
        if ($total) {
            $count_model = clone $model;
            $count = $count_model->where($where)->count();
        }
        if ($page > 0) {
            $model = $model->page($page);
        }
        if ($limit > 0) {
            $model = $model->limit($limit);
            $pages = ceil($count / $limit);
        }
        $list = $model->with(['platform','team'])->append(['platform_name','platform_sign','team_name','team_host','auth_num'])
            ->hidden(['platform','team'])->field($field)->where($where)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
    }
    public static function add($param)
    {
        $model = new PlatformAuthModel();
        $pk = $model->getPk();

        unset($param[$pk]);
        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();
        
        // 启动事务
        $model->startTrans();
        try {
            // 添加
            $model->save($param);
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param[$pk] = $model->$pk;

        return $param;
    }
    public static function edit($ids, $param = [])
    {
        $model = new PlatformAuthModel();
        $pk = $model->getPk();
        unset($param[$pk], $param['ids']);
        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $model->where($pk, 'in', $ids)->update($param);
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }

        $param['ids'] = $ids;

        return $param;
    }
    public static function dele($ids)
    {
        $model = new PlatformAuthModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $model->where($pk, 'in', $ids)->delete();
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            $errmsg = $e->getMessage();
            // 回滚事务
            $model->rollback();
        }

        if (isset($errmsg)) {
            exception($errmsg);
        }
        $update['ids'] = $ids;
        return $update;
    }

}
