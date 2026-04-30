<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\team;

use app\common\cache\team\TeamCache;
use app\common\cache\member\MemberCache;
use app\common\model\team\TeamModel;
/**
 * 团队
 */
class TeamService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'team_id/d'   => '',
        'team_name/s' => '',
        'team_logo/s' => '',
        'team_host/s' => '',
        'commission_ratio/s' => '',
        'auth_num/s'       => '',
        'sort/d'       => ''
    ];

    /**
     * 团队列表
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
    public static function list($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new TeamModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',team_name,team_logo,team_host,auth_num,commission_ratio,is_delete,is_default,is_disable,sort';
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
    public static function selectList(){
        $model = new TeamModel();
        return $model->field('team_id,team_name')->where('is_delete',0)->select()->toArray();
    }
    /**
     * 会员分组信息
     *
     * @param int  $id   分组id
     * @param bool $exce 不存在是否抛出异常
     * 
     * @return array|Exception
     */
    public static function info($id, $exce = true)
    {
        $info = TeamCache::get($id);
        if (empty($info)) {
            $model = new TeamModel();

            $info = $model->with(['logo'])->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('团队不存在：' . $id);
                }
                return [];
            }
            $info = $info->append(['logo_url'])
            ->hidden(['logo'])->toArray();
             
            TeamCache::set($id, $info);
        }

        return $info;
    }

    /**
     * 会员分组添加
     *
     * @param array $param 分组信息
     * 
     * @return array|Exception
     */
    public static function add($param)
    {
        $model = new TeamModel();
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

    /**
     * 会员分组修改
     *
     * @param int|array $ids   分组id
     * @param array     $param 分组信息
     * 
     * @return array|Exception
     */
    public static function edit($ids, $param = [])
    {
        $model = new TeamModel();
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

        TeamCache::del($ids);

        return $param;
    }

    /**
     * 团队删除
     *
     * @param array $ids  分组id
     * @param bool  $real 是否真实删除
     * 
     * @return array|Exception
     */
    public static function dele($ids, $real = false)
    {
        $model = new TeamModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            if ($real) {
                foreach ($ids as $id) {
                    $info = $model->find($id);
                    // 删除接口
                    $info->apis()->detach();
                }
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

        TeamCache::del($ids);

        return $update;
    }

}
