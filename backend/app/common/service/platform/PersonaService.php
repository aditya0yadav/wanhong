<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\platform;

use app\common\model\persona\PersonaModel;
use app\common\model\persona\PersonaDataModel;
use app\common\model\platform\PlatformModel;
/**
 * 人设模版
 */
class PersonaService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'persona_id/d'   => '',
        'persona_name/s' => '',
        'persona_type/d' => 0,
        'sort/d'=>0
    ];
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new PersonaModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',persona_id,persona_name,persona_type,sort';
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
        $model = new PersonaModel();
            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('人设模版不存在：' . $id);
                }
                return [];
            }
            $info = $info
            ->toArray();

        return $info;
    }
    public static function add($param)
    {
        $model = new PersonaModel();
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
        $model = new PersonaModel();
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
        $model = new PersonaModel();
        $pk = $model->getPk();
    
        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $PlatformModel = new PlatformModel();
            if($PlatformModel->where('platform_persona_template','in',$ids)->find()){
                exception('平台正在使用该模版,请先解除');
            }
            $PersonaDataModel = new PersonaDataModel();
            $PersonaDataModel->where('persona_id','in',$ids)->delete();
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
    public static function copy($ids)
    {
        $model = new PersonaModel();
        $dataModel = new PersonaDataModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $data = $model->where($pk, 'in', $ids)->select()->toArray();
            foreach ($data as $row){
                unset($row['persona_id']);
                $row['persona_name'] = $row['persona_name'].'_copy';
                $row['create_uid']  = user_id();
                $row['create_time'] = datetime();
                $row['update_uid']  = user_id();
                $row['update_time'] = datetime();
                $row['delete_uid']  = 0;
                $row['delete_time'] = NULL;
                $model->save($row);
                $insertId = $model->persona_id;
                $personaData = $dataModel->where($pk, 'in', $ids)->select()->toArray();
                $batchData = [];
                foreach ($personaData as $persona){
                    unset($persona['persona_data_id']);
                    $persona['persona_id'] = $insertId;
                    $persona['create_uid']  = user_id();
                    $persona['create_time'] = datetime();
                    $persona['update_uid']  = user_id();
                    $persona['update_time'] = datetime();
                    $persona['delete_uid']  = 0;
                    $persona['delete_time'] = NULL;
                    $batchData[] = $persona;
                }
                $dataModel->insertAll($batchData);
            }
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
        return true;
    }

}
