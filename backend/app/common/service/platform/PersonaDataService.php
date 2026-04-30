<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\platform;

use app\common\model\persona\PersonaDataModel;
/**
 * 人设模版内容
 */
class PersonaDataService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'persona_data_id/d'   => '',
        'persona_id/d'   => '',
        'persona_data_name/s' => '',
        'persona_data_type/s' => '',
        'persona_data_values/s' => '',
        'persona_data_holder/s' => '',
        'persona_data_must/d' => 0,
        'sort/d'=>0
    ];
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new PersonaDataModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',persona_id,persona_data_name,persona_data_type,persona_data_holder,persona_data_values,persona_data_must,sort';
        } else {
            $field = $group . ',' . $field;
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $model = $model->alias('a');
        $where = array_values($where);

        $append = [];
        if (strpos($field, 'is_default')) {
            $append[] = 'is_default_name';
        }
        if (strpos($field, 'is_disable')) {
            $append[] = 'is_disable_name';
        }

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
        $list = $model->field($field)->where($where)->append($append)->order($order)->select()->toArray();
        return compact('count', 'pages', 'page', 'limit', 'list');
    }
    public static function info($id, $exce = true)
    {
        $model = new PersonaDataModel();
            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('人设不存在：' . $id);
                }
                return [];
            }
            $info = $info
            ->toArray();

        return $info;
    }
    public static function add($param)
    {
        $model = new PersonaDataModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();
        $param['delete_uid']  = 0;
        $param['delete_time'] = '0000-00-00';
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
        $model = new PersonaDataModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();
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
        $model = new PersonaDataModel();
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
