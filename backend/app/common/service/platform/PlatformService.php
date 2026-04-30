<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\platform;

use app\common\cache\platform\PlatformCache;
use app\common\model\platform\PlatformModel;
use app\common\model\platform\PlatformAuthModel;
use app\common\model\platform\CurrencyModel;
use app\common\model\project\ProjectModel;
use app\common\model\team\RewardModel;
/**
 * 平台
 */
class PlatformService
{
    /**
     * 添加修改字段
     * @var array
     */
    public static $edit_field = [
        'platform_id/d'   => '',
        'platform_name/s' => '',
        'platform_sign/s' => '',
        'platform_image/s'=>'',
        'platform_color/s'=>'',
        'platform_url/s'=>'',
        'platform_quota_url/s' =>'',
        'platform_click_url/s' =>'',
        'platform_level/d'=>5,
        'platform_currency/d'   => 0,
        'params/a' => [],
        'project_params/a' => [],
        'is_list/d' => 0,
        'is_wall/d' => 0,
        'is_persona/d' => 0,
        'platform_persona_template/d' =>0,
        'platform_persona_backend/d' => 0,
        'is_quota/d' => 0,
        'show_quota/d' => 1,
        'show_click/d' => 1,
        'show_complete/d' => 1,
        'show_loi/d' => 1,
        'show_ir/d' => 1,
        'show_no/d' => 1,
        'is_disable' => 0,
        'is_custom' => 0,
        'is_accept_error' => 0,
        'is_hand' => 0,
        'model_type' => 0,
        'pay_type' => 0,
        'sort/d'       => 0,
        'limit_endtime/d'   => 0,
    ];

    /**
     * 平台列表
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
        $model = new PlatformModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',platform_name,platform_sign,is_delete,is_disable,platform_image,platform_level,sort';
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
        $with = [];
        $hidden= [];
        if (strpos($field, 'platform_image')) {
            $with[]   = $hidden[] = 'logo';
            $append[] = 'logo_url';
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
        $list = $model->with($with)->withCount('projects', false)->field($field)->where($where)->append($append)->hidden($hidden)->order($order)->select()->toArray();
        return compact('count', 'pages', 'page', 'limit', 'list');
    }
    /**
     * 货币列表
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
    public static function currencyList($where = [], $page = 1, $limit = 10,  $order = [], $field = '', $total = true)
    {
        $model = new CurrencyModel();
        $pk = $model->getPk();
        $group = 'a.' . $pk;

        if (empty($field)) {
            $field = $group . ',currency_code,currency_name,currency_coins';
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
        $list = $model->field($field)->where($where)->order($order)->select()->toArray();
        return compact('count', 'pages', 'page', 'limit', 'list');
    }
    public static function currencyAdd($param)
    {
        $model = new CurrencyModel();
        $pk = $model->getPk();
        unset($param[$pk]);
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
    public static function currencyEdit($ids, $param = [])
    {
        $model = new CurrencyModel();
        $pk = $model->getPk();
        unset($param[$pk], $param['ids']);
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
        return $param;
    }
    public static function currencyDele($ids)
    {
        $model = new CurrencyModel();
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
    public static function selectList(){
        $model = new PlatformModel();
        return $model->field('platform_id,platform_name')->where(where_disdel())->select()->toArray();
    }
    public static function all(){
        $model = new PlatformModel();
        return $model->where(where_disdel())->select()->toArray();
    }
    public static function currencyAllList(){
        $model = new CurrencyModel();
        return $model->select()->toArray();
    }
    public static function statistic($id,$param){
        $model = new RewardModel();
        if(empty($param['date_value'])){
            // 获取时间范围（默认最近30天）
            $end = new \DateTime('23:59:59');
            $start = new \DateTime('-30 days');
        } else {
            // 获取时间范围（默认最近30天）
            $end = new \DateTime($param['date_value'][1]);
            $start = new \DateTime($param['date_value'][0]);
        }
        $start->setTime(0, 0);
        $daysDiff = $end->diff($start)->days;
        if ($daysDiff <= 1) {
        $timeFormat = "%Y-%m-%d %H:00:00";
        $groupFormat = 'Y-m-d H:00:00';
        $interval = 'PT1H';  // 每小时
        } elseif ($daysDiff <= 30) {
        $timeFormat = "%Y-%m-%d";
        $groupFormat = 'Y-m-d';
        $interval = 'P1D';    // 每天
        } elseif ($daysDiff <= 365) {
        $timeFormat = "%Y-%m";
        $groupFormat = 'Y-m';
        $interval = 'P1M';    // 每月
        } else {
        $timeFormat = "%Y";
        $groupFormat = 'Y';
        $interval = 'P1Y';    // 每年
        }

    // 对齐时间边界
    $alignedStart = clone $start;
    $alignedEnd = clone $end;
    switch($interval) {
        case 'PT1H':
            $alignedStart->setTime($alignedStart->format('H'), 0);
            break;
        case 'P1M':
            $alignedStart->modify('first day of this month');
            $alignedEnd->modify('last day of this month');
            break;
        case 'P1Y':
            $alignedStart->modify('first day of January this year');
            $alignedEnd->modify('last day of December this year');
            break;
    }

    // 构建SQL字段
    $field = "
        DATE_FORMAT(create_time, '{$timeFormat}') as period,
        COUNT(*) as total,
        ROUND(SUM(payout / usd_currency_coins),2) as total_payout,
        ROUND(SUM(team_payout / usd_currency_coins),2) as total_team_payout,
        ROUND(SUM(member_payout / usd_currency_coins),2) as total_member_payout
    ";

    // 执行查询
    $data = $model
        ->field($field)
        ->where('platform_id', $id)
        ->where('reward_status','in', [0,1])
        ->where('create_time', 'BETWEEN', [$alignedStart->format('Y-m-d H:i:s'), $alignedEnd->format('Y-m-d H:i:s')])
        ->group('period')
        ->order('create_time ASC')
        ->select();
    $periods = [];
    $current = clone $alignedStart;
    while($current <= $alignedEnd) {
        $periods[] = $current->format($groupFormat);
        $current->add(new \DateInterval($interval));
    }

    // 构建数据映射表
    $dataMap = [];
    foreach($data as $item) {
        $dataMap[$item['period']] = $item;
    }
    
    // 合并数据并补全空缺
    $result = [];
    foreach($periods as $period) {
        if(isset($dataMap[$period])) {
            $result[] = $dataMap[$period];
        } else {
            $result[] = [
                'total' =>0,
                'period' => $period,
                'total_payout' => 0.00,
                'total_team_payout' => 0.00,
                'total_member_payout' => 0.00
            ];
        }
    }
        $chartData['title'] = '平台收益统计';
        $chartData['selected'] = ['完成数量' => false];
        foreach ($result as $key => $v){
            $offers[$v['period']] = $v['total'];
            $all_sy[$v['period']] = $v['total_payout'];
            $team_sy[$v['period']] = $v['total_team_payout'];
            $member_sy[$v['period']] = $v['total_member_payout'];
            $columnData[] = $v['period'];
        }
        $series = [
            ['name' => '完成数量', 'type' => 'line', 'data' => array_values($offers), 'label' => ['show' => true, 'position' => 'top','formatter' => '{c}个']],
            ['name' => '总收益', 'type' => 'line', 'data' => array_values($all_sy), 'label' => ['show' => true, 'position' => 'top','formatter' => '${c}']],
            ['name' => '团队收益', 'type' => 'line', 'data' => array_values($team_sy), 'label' => ['show' => true, 'position' => 'top','formatter' => '${c}']],
            ['name' => '会员收益', 'type' => 'line', 'data' => array_values($member_sy), 'label' => ['show' => true, 'position' => 'top','formatter' => '${c}']],
        ];
        $legend = array_column($series, 'name');
        $chartData['legend'] = $legend;
        $chartData['xAxis']  = $columnData;
        $chartData['series'] = $series;
        $chartData['start'] = $start->format('Y-m-d H:i:s');
        $chartData['end'] = $end->format('Y-m-d H:i:s');
        return $chartData;
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
        $info = PlatformCache::get($id);
        if (empty($info)) {
            $model = new PlatformModel();
            $info = $model->with(['logo'])->find($id);
            if (empty($info)) {
                if ($exce) {
                    exception('平台不存在：' . $id);
                }
                return [];
            }
            $info = $info
            ->append(['logo_url'])
            ->hidden(['logo'])
            ->toArray();
            // 获取当前的微秒时间戳
            $microtime = microtime(true);
            // 转换为十三位整数时间戳（毫秒级）
            $timestamp = round($microtime * 1000);
            $info['params'] = $info['params'] ? json_decode($info['params']):[];
            $info['params_default'] = [['key'=>$timestamp,'name' => '','value' => '']];
            $info['project_params'] = $info['project_params'] ? json_decode($info['project_params']):[];
            $info['project_params_default'] = [['key'=>$timestamp,'name' => '','field' => '','value' => '']];
            PlatformCache::set($id, $info);
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
        $model = new PlatformModel();
        $pk = $model->getPk();

        unset($param[$pk]);

        $param['create_uid']  = user_id();
        $param['create_time'] = datetime();
        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();
        $param['delete_uid']  = 0;
        $param['delete_time'] = '0000-00-00';
        
        if(count($param['params']) == 1 && !$param['params'][0]['name']){
               $param['params'] = NULL; 
        }
        if(count($param['project_params']) == 1 && !$param['project_params'][0]['name']){
           $param['project_params'] = NULL; 
        }
        $param['params'] = $param['params'] ? json_encode($param['params']): NULL;
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
        $model = new PlatformModel();
        $pk = $model->getPk();

        unset($param[$pk], $param['ids']);

        $param['update_uid']  = user_id();
        $param['update_time'] = datetime();
        if(isset($param['params']) && count($param['params']) == 1 && !$param['params'][0]['name']){
           $param['params'] = NULL; 
        }
        if(isset($param['project_params']) && count($param['project_params']) == 1 && !$param['project_params'][0]['name']){
           $param['project_params'] = NULL; 
        }
        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $projectModel = new ProjectModel();
            /*
            //同步平台项目对应自定义参数
            if($param['project_params']){
                $lists = $projectModel->where('platform_id', 'in', $ids)->select();
                foreach ($lists as $list){
                    if($list['project_params']){
                        $projectParams = json_decode($list['project_params'],true);
                        $data = [];
                        foreach ($param['project_params'] as $k => $v){
                            foreach ($projectParams as $projectParam){
                                if($v['field'] == $projectParam['field']){
                                    $v['value'] = $projectParam['value'];
                                }
                            }
                            $data[] = $v;
                        }
                        $projectModel->where('project_id','=',$list['project_id'])->update(['project_params'=>json_encode($data)]);
                    } else {
                        $projectModel->where('project_id','=',$list['project_id'])->update(['project_params'=>json_encode($param['project_params'])]);
                    }
                }
            } else {
                $projectModel->where('platform_id', 'in', $ids)->update(['project_params' => NULL]);
            }*/
            if(isset($param['project_params'])){
                $projectModel->where('platform_id', 'in', $ids)->update(['project_params'=>json_encode($param['project_params'])]);
                $param['project_params'] = $param['project_params'] ? json_encode($param['project_params']) : NULL;
            }
            if(isset($param['params'])){
                // 修改
                $param['params'] = $param['params'] ? json_encode($param['params']): NULL;
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

        PlatformCache::del($ids);

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
        $model = new PlatformModel();
        $pk = $model->getPk();

        // 启动事务
        $model->startTrans();
        try {
            if (is_numeric($ids)) {
                $ids = [$ids];
            }
            $projectModel = new ProjectModel();
            $projectModel->where('platform_id','in',$ids)->delete();
            $platformAuthModel = new PlatformAuthModel();
            $platformAuthModel->where('platform_id','in',$ids)->delete();
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

        PlatformCache::del($ids);

        return $update;
    }

}
