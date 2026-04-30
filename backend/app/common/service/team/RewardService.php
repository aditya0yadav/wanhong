<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\team;

use app\common\cache\team\RewardCache;
use app\common\model\team\RewardModel;
use app\common\service\file\ExportService as FileExportService;
use app\common\service\file\ImportService as FileImportService;
use app\common\model\member\MemberModel;
use DateTime;
/**
 * 团队
 */
class RewardService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'reward_id/d' => '',
        'team_id/d'   => '',
        'platform_id/d' => '',
        'member_id/d' => '',
        'txn_id/s' => '',
        'payout/s' => '',
        'team_payout/s' => '',
        'member_payout/s' => '',
        'reward_status/d' => 2
    ];
    //业绩状态
    public static $rewardStatus = [
            1=>'成功',
            2=>'失败',
            3=>'超限',
            4=>'终止',
            5=>'未知',
            6=>'核减'
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
        $model = new RewardModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;
        
        if (empty($field)) {
            $field = $group . ',platform_id,team_id,member_id,txn_id,project_pno,project_no,project_name,create_time,start_time,reward_status,payout,team_payout,member_payout,ip,front_rs,backend_rs,auth_time,uuid,ua';
        } else {
            $field = $group . ',' . $field;
        }
        if (empty($order)) {
            $order = [$pk => 'desc'];
        }

        $model = $model->alias('a');
        $where = array_values($where);

        $append = [];
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
        $list = $model->with(['platform'=> function($query) {
        $query->field('platform_id,platform_name');
    },'team'=> function($query) {
        $query->field('team_id,team_name');
    },'member'=> function($query) {
        $query->field('member_id,nickname,avatar_id');
    }])->field($field)->where($where)->append($append)->order($order)->select()->toArray();

        return compact('count', 'pages', 'page', 'limit', 'list');
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
        $info = RewardCache::get($id);
        if (empty($info)) {
            $model = new RewardModel();

            $info = $model->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('记录不存在：' . $id);
                }
                return [];
            }
            $info = $info->toArray();

            RewardCache::set($id, $info);
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
        $model = new RewardModel();
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
        $model = new RewardModel();
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

        RewardCache::del($ids);

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
        $model = new RewardModel();
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

        RewardCache::del($ids);

        return $update;
    }
    public static function export($param,$type = 2)
    {
        $exportType = $param['export_type'] ?? 'xlsx';
        $export = [
            'type'       => FileExportService::TYPE_REWARD,
            'file_path'  => ExportService::$file_dir . '/member-' . date('YmdHis') . '-' . uniqids() . '.'.$exportType,
            'file_name'  => '业绩导出-' . date('Ymd-His') . '.'.$exportType,
            'param'      => ['where' => $param['where'], 'order' => $param['order']],
            'remark'     => $param['export_remark'] ?? '',
            'create_uid' => user_id(),
        ];
        $export_id = FileExportService::add($export);
        return ExportService::reward(['export_id' => $export_id,'export_type' => $exportType],$type);
    }
    public static function customStatistic($param){
        $where =[];
        if($param['platform_id']){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        if($param['team_id']){
            $where[] = ['team_id','=',$param['team_id']];
        }
        if($param['member']){
            $memberModel = new MemberModel();
            $member= $memberModel->where('nickname','=',$param['member'])->find();
            $where[] = ['member_id','=',$member ? $member['member_id']:-1];
        }
        if($param['search_value']){
            $where[] = ['project_name','=',$param['search_value']];
        }
        if(!empty($param['date_value'])){
            $where[] = ['create_time','between',[$param['date_value'][0],$param['date_value'][1]]];
        }
        $model = new RewardModel();
        $field = "COUNT(*) AS total,
            SUM(CASE reward_status WHEN 6 THEN 1 ELSE 0 END) AS deduction_count,
            SUM(CASE reward_status WHEN 1 THEN 1 ELSE 0 END) AS success_count,
            SUM(CASE WHEN reward_status > 1 THEN 1 ELSE 0 END) AS failed_count,
            ROUND((SUM(CASE WHEN reward_status = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)),2) AS success_rate,
            ROUND(SUM(CASE WHEN reward_status = 6 THEN payout / usd_currency_coins ELSE 0 END),2) AS deduction_payout,
            ROUND(SUM(CASE WHEN reward_status = 1 THEN payout / usd_currency_coins ELSE 0 END),2) AS success_payout,
            ROUND(SUM(CASE WHEN reward_status > 1 THEN payout / usd_currency_coins ELSE 0 END),2) AS failed_payout,
            ROUND(SUM(CASE WHEN reward_status = 6 THEN team_payout / usd_currency_coins ELSE 0 END),2) AS deduction_team_payout,
            ROUND(SUM(CASE WHEN reward_status = 1 THEN team_payout / usd_currency_coins ELSE 0 END),2) AS success_team_payout,
            ROUND(SUM(CASE WHEN reward_status > 1 THEN team_payout / usd_currency_coins ELSE 0 END),2) AS failed_team_payout,
            ROUND(SUM(CASE WHEN reward_status = 6 THEN member_payout / usd_currency_coins ELSE 0 END),2) AS deduction_member_payout,
            ROUND(SUM(CASE WHEN reward_status = 1 THEN member_payout / usd_currency_coins ELSE 0 END),2) AS success_member_payout,
            ROUND(SUM(CASE WHEN reward_status > 1 THEN member_payout / usd_currency_coins ELSE 0 END),2) AS failed_member_payout";
        $data = $model->field($field)->where($where)->select();
        return $data;
    }
    public static function statistic($type = 'week', $date = [])
    {
        $column = [];
        if (empty($date)) {
            $date = [];
            if ($type == 'week') {
                $starttime = date('Y-m-d 00:00:00',strtotime('-6 days'));
                $endtime = date('Y-m-d H:i:s');
                for ($time = strtotime($starttime); $time <= strtotime($endtime);) {
                    $column[] = date("m-d", $time);
                    $time += 86400;
                }
                $columnData = array_fill_keys($column, 0);
            } else if($type == 'month') {
                $starttime = date('Y-m-d 00:00:00',strtotime('-29 days'));
                $endtime = date('Y-m-d H:i:s');
                for ($time = strtotime($starttime); $time <= strtotime($endtime);) {
                    $column[] = date("m-d", $time);
                    $time += 86400;
                }
                $columnData = array_fill_keys($column, 0);
            } else if($type == 'year') {
                $starttime = date('Y-m-d 00:00:00',strtotime('-11 months'));
                $endtime = date('Y-m-d H:i:s');
                for ($time = strtotime($starttime); $time <= strtotime($endtime);) {
                    $column[] = date("Y-m", $time);
                    $date = new DateTime();
                    $date->setDate(date('Y', $time), date('m', $time), 1);
                    // 修改日期到下一个月的第一天，然后减去一天，这样我们就能得到上一个月的最后一天，即本月的最后一天
                    $date->modify('first day of next month');
                    $date->modify('-1 day');
                    // 获取天数
                    $daysInMonth = $date->format('t');
                    $time += $daysInMonth * 86400;
                }
                $columnData = array_fill_keys($column, 0);
            }
        }
        if ($type == 'week' || $type == 'month') {
            $field = "count(create_time) as num,round(sum(payout / usd_currency_coins),2) as all_sy,round(sum(team_payout / usd_currency_coins),2) as team_sy,round(sum(member_payout / usd_currency_coins),2) as member_sy, date_format(create_time,'%m-%d') as date";
            $group = "date_format(create_time,'%m-%d')";
        } else if($type=='year') {
            $field = "count(create_time) as num,round(sum(payout / usd_currency_coins),2) as all_sy,round(sum(team_payout / usd_currency_coins),2) as team_sy,round(sum(member_payout / usd_currency_coins),2) as member_sy, date_format(create_time,'%Y-%m') as date";
            $group = "date_format(create_time,'%Y-%m')";
        }
        $where[] = ['reward_status', 'in', [0,1]];
        $where[] = ['create_time', 'between', [$starttime,$endtime]];
        $model = new RewardModel();
        $data['title'] = '收益图';
        $data['selected'] = ['完成数量' => false];
        $adds = $model->field($field)->where($where)->group($group)->select();
        $offers = $columnData;
        $all_sy = $columnData;
        $team_sy = $columnData;
        $member_sy = $columnData;
        foreach ($adds as $key => $v){
            $offers[$v['date']] = $v['num'];
            $all_sy[$v['date']] = $v['all_sy'];
            $team_sy[$v['date']] = $v['team_sy'];
            $member_sy[$v['date']] = $v['member_sy'];
        }
        $series = [
            ['name' => '完成数量', 'type' => 'line', 'data' => array_values($offers), 'label' => ['show' => true, 'position' => 'top','formatter' => '{c}个']],
            ['name' => '总收益', 'type' => 'line', 'data' => array_values($all_sy), 'label' => ['show' => true, 'position' => 'top','formatter' => '${c}']],
            ['name' => '团队收益', 'type' => 'line', 'data' => array_values($team_sy), 'label' => ['show' => true, 'position' => 'top','formatter' => '${c}']],
            ['name' => '会员收益', 'type' => 'line', 'data' => array_values($member_sy), 'label' => ['show' => true, 'position' => 'top','formatter' => '${c}']],
        ];
        $legend = array_column($series, 'name');

        $data['type']   = $type;
        $data['date']   = $date;
        $data['legend'] = $legend;
        $data['xAxis']  = array_keys($columnData);
        $data['series'] = $series;
        return $data;
    }
}
