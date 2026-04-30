<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\project;

use app\common\cache\project\ProjectCache;
use app\common\model\project\ProjectModel;
use app\common\model\platform\PlatformModel;
use app\common\service\file\FileService;
/**
 * 项目
 */
class ProjectService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'project_id/d'   => '',
        'project_no/s' => '',
        'platform_id/d' =>'',
        'project_name/s' => '',
        'project_code/s'=>'',
        'project_content/s'=>'',
        'project_file_id/d'=>0,
        'project_image_url/s'=>'',
        'project_click_url/s'=>'',
        'project_click/d' => 0,
        'project_complete/d' => 0,
        'project_quota/d' => 0,
        'project_persona_template/d' => 0,
        'project_persona_backend/d' => 0,
        'project_loi/s' => '',
        'project_ir/s' => '',
        'project_cpi/s' => '',
        'project_icon/s' => '',
        'is_recommend/d' => 0,
        'project_currency/s' => '',
        'project_category/s' => '',
        'project_params/a' => [],
        'sort/d'=>0
    ];

    /**
     * 项目列表
     *
     * @param array  $where 条件
     * @param int    $page  页数
     * @param int    $limit 数量
     * @param array  $order 排序
     * @param string $field 字段
     * @param bool   $total 总数
     * 
     * @return array ['count', 'pages', 'page', 'limit', 'list']
     */
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true,
    $countRewards = false)
    {
        $model = new ProjectModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',project_id,platform_id,project_pno,project_no,project_name,project_content,project_code,project_click_url,project_click,project_complete,project_quota,project_loi,project_ir,project_cpi,project_icon,project_currency,project_category,project_image_url,create_time,update_time,is_disable,is_delete,delete_time,project_params';
        } else {
            $field = $group . ',' . $field;
        }
        if (empty($order)) {
            $order = ['sort' => 'desc', $pk => 'desc'];
        }

        $model = $model->alias('a');
        $where = array_values($where);

        $append = [];
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
        $list = $model->with(['currency','platform' => function ($query){
                return $query->field('platform_id,platform_name,is_quota,show_no,show_loi,show_ir,show_quota,is_persona,show_click,show_complete');
        }])->field($field)->where($where)->append($append)->order($order)->select()->toArray();
        return compact('count', 'pages', 'page', 'limit', 'list');
    }
    public static function info($id, $exce = true)
    {
        $model = new ProjectModel();

            $info = $model->with(['currency','platform'=>function ($query){
                return $query->field('platform_id,platform_name,platform_sign,is_quota');
            }])->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('项目不存在：' . $id);
                }
                return [];
            }
            $info = $info
            ->append(['project_file_url'])
            ->toArray();
            $platModel = new PlatformModel();
            $platInfo = $platModel->find($info['platform_id']);
            $info['project_params'] = $info['project_params'] ? json_decode($info['project_params']):($platInfo['project_params'] ? json_decode($platInfo['project_params']) : []);
        return $info;
    }
    public static function add($param)
    {
        $model = new ProjectModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();
        $param['project_params'] = $param['project_params'] ? json_encode($param['project_params']) : NULL;
        
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
        $model = new ProjectModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();
        $param['project_params'] = !empty($param['project_params']) ? json_encode($param['project_params']) : NULL;
        $param['project_sign'] = md5($param['platform_id'].$param['project_no'].$param['project_code']);
        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            // 修改
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

        ProjectCache::del($ids);

        return $param;
    }
    public static function clearReclycle($id){
        $model = new ProjectModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if($id){
               $model->where('platform_id', '=', $id)->where('is_delete',1)->delete();
            } else {
               $model->where('is_delete',1)->delete(); 
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
        return 1;
    }
    public static function restore($id){
        $model = new ProjectModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($id)) {
                $ids = [$id];
            }
            $model->where($pk, 'in', $ids)->update(['is_delete'=>0,'delete_uid'=>0,'delete_time'=>NULL]);
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

        ProjectCache::del($id);

        return $update;
    }
    public static function dele($ids, $real = false)
    {
        $model = new ProjectModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                $model->where($pk, 'in', $ids)->delete();
            } else {
                $update = delete_update();
                $model->where($pk, 'in', $ids)->update($update);
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

        $update['ids'] = $ids;

        ProjectCache::del($ids);

        return $update;
    }
    public static function copy($ids)
    {
        $model = new ProjectModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $data = $model->where($pk, 'in', $ids)->select()->toArray();
            foreach ($data as $row){
                unset($row['project_id']);
                $row['project_pno'] = general_pno();
                $row['project_no'] = $row['project_no'].'_copy';
                $row['project_name'] = $row['project_name'].'_copy';
                $row['project_sign'] = md5($row['platform_id'].$row['project_no'].$row['project_code']);
                $row['create_uid']  = user_id();
                $row['create_time'] = datetime();
                $row['update_uid']  = user_id();
                $row['update_time'] = datetime();
                $row['delete_uid']  = 0;
                $row['delete_time'] = NULL;
                $model->save($row);
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

        $update['ids'] = $ids;
        return $update;
    }

}
