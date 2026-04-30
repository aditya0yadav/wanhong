<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\team;

use app\common\controller\BaseController;
use app\common\validate\team\RewardValidate;
use app\common\service\team\RewardService;
use app\common\service\team\ImportService;
use app\common\model\team\RewardModel;
use app\common\model\member\MemberModel;
use app\common\model\platform\PlatformModel;
use app\common\model\project\ProjectModel;
use hg\apidoc\annotation as Apidoc;

/**
 * @Apidoc\Title("业绩")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("300")
 */
class Reward extends BaseController
{
    public function list()
    {
        $where = $this->where();
        $param = $this->params(['team_id/s' => '','platform_id/s'=>'','reward_status/s'=>'','member/s'=>'','rs/d'=>0]);
        if($param['team_id']){
            $where[] = ['team_id','=',$param['team_id']];
        }
        if($param['platform_id']){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        if(in_array($param['reward_status'],[1,2,3,4,5,6])){
            $where[] = ['reward_status','=',$param['reward_status']];
        }
        if($param['rs']){
            $where[] = ['backend_rs','<>',''];
        }
        if($param['member']){
            $memberModel = new MemberModel();
            $member = $memberModel->where('member_id',$param['member'])->whereOr('nickname',$param['member'])->whereOr('username',$param['member'])->find();
            $where[] = ['member_id','=',$member ? $member['member_id'] : -1];
        }
        $data = RewardService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        return success($data);
    }
    public function info()
    {
        $param = $this->params(['reward_id/d' => '']);

        validate(RewardValidate::class)->scene('info')->check($param);
        
        $data = RewardService::info($param['reward_id']);

        return success($data);
    }
    public function uuidAudit(){
        $param = $this->params(['auth_type/d' => 0,'ids/a'=>[]]);
        $rewardModel = new RewardModel();
        $dateTime = date('Y-m-d H:i:s');
        if(empty($param['ids'])){
            return error('选择数据不能为空'); 
        }
        $where[] = ['reward_id','in',$param['ids']];
        $where[] = ['reward_status','=',1];
        $rewardModel->where($where)->update(['reward_status'=> 6,'auth_time'=>$dateTime ]);
        return success('核减完成');
    }
    public function batchAudit(){
    $param = $this->params([
        'auth_type/d' => '',
        'auth_sync/d' => '',
        'auth_date/a' => [],
        'auth_uuids/s' => '',
        'tb_type/d' => 0,
        'tb_id/s' => '',
        'tb_no/s' => '',
        'tb_pt/s' => ''
    ]);

    $uuids = explode("\n", $param['auth_uuids']);
    $uuids = array_map('trim', $uuids);
    $uuids = array_filter($uuids);

    $rewardModel = new RewardModel();

    if (count($uuids) == 0) {
        return error('UUID数据不能为空');
    }
    if ($param['auth_sync'] == 1 && empty($param['auth_date'][0])) {
        return error('同步必须选择起止时间');
    }

    $dateTime = date('Y-m-d H:i:s');

    // =========== 同步逻辑 ===========
    if ($param['auth_sync'] == 1) {
        // 校验同步目标（platform / project / pno）
        if ($param['tb_type'] == 0) {
            if (!$param['tb_pt']) return error('请选择同步平台');
            $platformModel = new PlatformModel();
            $platform = $platformModel->where('platform_id', $param['tb_pt'])->find();
            if (!$platform) return error('未知的同步平台');
        } elseif ($param['tb_type'] == 1) {
            if (!$param['tb_no']) return error('请输入同步编号');
            $projectModel = new ProjectModel();
            $project = $projectModel->where('project_no', $param['tb_no'])->find();
            if (!$project) return error('未知的同步编号');
        } elseif ($param['tb_type'] == 2) {
            if (!$param['tb_id']) return error('请输入同步PID');
            $projectModel = new ProjectModel();
            $project = $projectModel->where('project_pno', $param['tb_id'])->find();
            if (!$project) return error('未知的同步PID');
        } else {
            return error('未知的同步类型');
        }

        // 映射：auth_type -> reward_status
        $targetStatus = ($param['auth_type'] == 1) ? 1 : 6;     // auth_type 1 -> reward_status 1; auth_type 0 -> 6
        $reverseStatus = ($param['auth_type'] == 1) ? 6 : 1;    // 反状态

        // 使用事务，分两次批量更新（IN / NOT IN）
        \think\Db::startTrans();
        try {
            // 构建 in-list 查询并应用筛选条件
            $queryIn = $rewardModel->whereBetween('create_time', [$param['auth_date'][0], $param['auth_date'][1]]);
            if ($param['tb_type'] == 0) {
                $queryIn->where('platform_id', $param['tb_pt']);
            } elseif ($param['tb_type'] == 1) {
                $queryIn->where('project_no', $param['tb_no']);
            } elseif ($param['tb_type'] == 2) {
                $queryIn->where('project_pno', $param['tb_id']);
            }

            // 更新在传入 UUID 列表中的记录为目标状态
            if (!empty($uuids)) {
                $queryIn->whereIn('uuid', $uuids)
                        ->update(['reward_status' => $targetStatus, 'auth_time' => $dateTime]);
            }

            // 构建 not-in 查询并应用相同筛选条件
            $queryNotIn = $rewardModel->whereBetween('create_time', [$param['auth_date'][0], $param['auth_date'][1]]);
            if ($param['tb_type'] == 0) {
                $queryNotIn->where('platform_id', $param['tb_pt']);
            } elseif ($param['tb_type'] == 1) {
                $queryNotIn->where('project_no', $param['tb_no']);
            } elseif ($param['tb_type'] == 2) {
                $queryNotIn->where('project_pno', $param['tb_id']);
            }

            // 更新不在传入 UUID 列表中的记录为反状态
            // 注意：如果 uuids 为空数组，上面已被阻止（函数开头做了非空校验），此处仍做防护
            if (!empty($uuids)) {
                $queryNotIn->whereNotIn('uuid', $uuids)
                           ->update(['reward_status' => $reverseStatus, 'auth_time' => $dateTime]);
            }

            \think\Db::commit();
            return success('同步更新完成');
        } catch (\Exception $e) {
            \think\Db::rollback();
            return error('同步更新失败：' . $e->getMessage());
        }
    }

    // =========== 非同步逻辑（逐条处理）===========
    // auth_type: 0 -> 失败(6)，1 -> 成功(1)
    if ($param['auth_type'] == 0) {
        foreach ($uuids as $value) {
            $row = $rewardModel->where('uuid', $value)->find();
            if ($row) {
                $row->reward_status = 6;
                $row->auth_time = $dateTime;
                $row->save();
            }
        }
        return success('批量核减完成');
    }

    if ($param['auth_type'] == 1) {
        foreach ($uuids as $value) {
            $row = $rewardModel->where('uuid', $value)->find();
            if ($row) {
                $row->reward_status = 1;
                $row->auth_time = $dateTime;
                $row->save();
            }
        }
        return success('批量审核成功完成');
    }

    return error('未识别的操作');
}

    /**
     * @Apidoc\Title("团队添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel", field="group_name,group_desc,remark,sort")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel\getApiIdsAttr", field="api_ids")
     */
    public function add()
    {
        $param = $this->params(RewardService::$edit_field);

        validate(RewardValidate::class)->scene('add')->check($param);

        $data = RewardService::add($param);

        return success($data);
    }

    /**
     * @Apidoc\Title("会员分组修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel", field="group_id,group_name,group_desc,remark,sort")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel\getApiIdsAttr", field="api_ids")
     */
    public function edit()
    {
        $param = $this->params(RewardService::$edit_field);

        validate(RewardValidate::class)->scene('edit')->check($param);

        $data = RewardService::edit($param['reward_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("团队删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(RewardValidate::class)->scene('dele')->check($param);
        $data = RewardService::dele($param['ids']);

        return success($data);
    }
    public function export()
    {
        if ($this->request->isGet()) {
            $param = $this->params(['file_path/s' => '', 'file_name/s' => '']);
            return download($param['file_path'], $param['file_name']);
        }

        $param = $this->params(['export_remark/s' => '','team_id/s' => '','platform_id/s'=>'','reward_status/s'=>'','member/s'=>'','export_type'=>'csv']);
        $param['where'] = $this->where();
        if($param['team_id']){
            $param['where'][] = ['team_id','=',$param['team_id']];
        }
        if($param['platform_id']){
            $param['where'][] = ['platform_id','=',$param['platform_id']];
        }
        if(in_array($param['reward_status'],[0,1,2,3,4,5])){
            $param['where'][] = ['reward_status','=',$param['reward_status']];
        }
        if($param['member']){
            $memberModel = new MemberModel();
            $member = $memberModel->where('member_id',$param['member'])->whereOr('nickname',$param['member'])->whereOr('username',$param['member'])->find();
            $param['where'][] = ['member_id','=',$member ? $member['member_id'] : -1];
        }
        $param['order'] = $this->order();
        $data = RewardService::export($param,2);
        return success($data);
    }
    public function export1(){
        $param = $this->params(['export_remark/s' => '','team_id/s' => '','platform_id/s'=>'','reward_status/s'=>'','member/s'=>'']);
        $where = $this->where();
        if($param['team_id']){
            $where[] = ['team_id','=',$param['team_id']];
        }
        if($param['platform_id']){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        if(in_array($param['reward_status'],[0,1,2,3,4,5,6])){
            $where[] = ['reward_status','=',$param['reward_status']];
        }
        if($param['member']){
            $memberModel = new MemberModel();
            $member = $memberModel->where('member_id',$param['member'])->whereOr('nickname',$param['member'])->whereOr('username',$param['member'])->find();
            $where[] = ['member_id','=',$member ? $member['member_id'] : -1];
        }
        $order = $this->order() ?? [];
        $field = 'reward_id,project_pno,project_no,project_name,platform_id,team_id,member_id,front_rs,backend_rs,create_time,start_time,reward_status,ip,uuid';
        $page = 1;
        $limit = 50000;
        $list = RewardService::list($where, $page, $limit, $order, $field, false)['list'];
        return success($list);
    }
}
