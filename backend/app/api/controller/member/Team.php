<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\member;

use think\facade\Validate;
use app\common\controller\BaseController;
use hg\apidoc\annotation as Apidoc;
use app\common\model\platform\PlatformAuthModel;
use app\common\model\platform\PlatformModel;
use app\common\model\platform\FlowingModel;
use app\common\model\project\ProjectModel;
use app\common\model\team\RewardModel;
use app\common\model\member\MemberModel;
use app\common\service\member\MemberService;
use app\common\service\project\ProjectService;
use app\common\service\team\TeamService;
use app\common\service\team\RewardService;
use app\common\service\utils\Utils;
use app\common\service\member\TokenService;
use think\facade\Db;
/**
 * @Apidoc\Title("团队业绩")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("300")
 */
class Team extends BaseController
{
    public function rewards(){
        domain_verify();
        $where = $this->where();
        $where[] = ['team_id','=',member_team_id(true)];
        $where[] = ['member_id','=',member_id(true)];
        $param = $this->params([
            'platform_id/s' => '',
            'reward_status/s' => ''
        ]);
        if(!empty($param['platform_id'])){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        if(in_array($param['reward_status'],[1,2,3,4,5,6])){
            $where[] = ['reward_status','=',$param['reward_status']];
        }
        $data = RewardService::list($where, $this->page(), $this->limit(), $this->order(),'platform_id,member_payout,reward_status,ip,create_time,txn_id,project_pno,usd_currency_coins,is_mark');
        $data['exps'] = where_exps();
        return success($data);
    }
    public function team_rewards(){
        domain_verify();
        $param = $this->params([
            'platform_id/s' => '',
            'reward_status/s' => '',
            'member_nickname/s' => ''
        ]);
        $where = $this->where();
        $where[] = ['team_id','=',member_team_id(true)];
        if(!empty($param['platform_id'])){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        if(in_array($param['reward_status'],[1,2,3,4,5,6])){
            $where[] = ['reward_status','=',$param['reward_status']];
        }
        if(!empty($param['member_nickname'])){
            $memberModel = new MemberModel();
            $member= $memberModel->where('nickname','=',$param['member_nickname'])->find();
            $where[] = ['member_id','=',$member['member_id']];
        }
        
        $data = RewardService::list($where, $this->page(), $this->limit(), $this->order(),'platform_id,member_id,team_payout,member_payout,reward_status,ip,create_time,txn_id,project_pno,usd_currency_coins,is_mark');
        $data['exps'] = where_exps();
        return success($data);
    }
    public function statistics(){
        domain_verify();
        $where[] = ['team_id','=',member_team_id(true)];
        $where[] = ['member_id','=',member_id(true)];
        $param = $this->params([
            'platform_id/s' => '',
            'reward_status/s' => '',
            'date_value/a' => []
        ]);
        if(!empty($param['platform_id'])){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        if(in_array($param['reward_status'],[1,2,3,4,5,6])){
            $where[] = ['reward_status','=',$param['reward_status']];
        }
        if(count($param['date_value'])){
           $where[] = ['create_time','between',[$param['date_value'][0],$param['date_value'][1]]]; 
        }
        $rewardModel = new RewardModel();
        $offers = $rewardModel->where($where)->count();
        $deduction = $rewardModel->where($where)->where('reward_status',6)->sum('member_payout');
        $success = $rewardModel->where($where)->where('reward_status',1)->sum('member_payout');
        $failed = $rewardModel->where($where)->where('reward_status','>',1)->sum('member_payout');
        return success(['offers'=>$offers,'success'=>$success,'failed' => $failed,'deduction'=>$deduction]);
    }
    public function team_statistics(){
        domain_verify();
        $where[] = ['team_id','=',member_team_id(true)];
        $param = $this->params([
            'platform_id/s' => '',
            'reward_status/s' => '',
            'member_nickname/s' => '',
            'date_value/a' => []
        ]);
        if(!empty($param['platform_id'])){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        if(in_array($param['reward_status'],[1,2,3,4,5,6])){
            $where[] = ['reward_status','=',$param['reward_status']];
        }
        if(!empty($param['member_nickname'])){
            $memberModel = new MemberModel();
            $member= $memberModel->where('nickname','=',$param['member_nickname'])->find();
            $where[] = ['member_id','=',$member['member_id']];
        }
        if(count($param['date_value'])){
           $where[] = ['create_time','between',[$param['date_value'][0],$param['date_value'][1]]]; 
        }
        $offers = Db::name('reward')->where($where)->count();
        $teamdeduction = Db::name('reward')->where($where)->where('reward_status',6)->sum('team_payout');
        $teamsuccess = Db::name('reward')->where($where)->where('reward_status',1)->sum('team_payout');
        $teamfailed = Db::name('reward')->where($where)->where('reward_status','>',1)->sum('team_payout');
        $memberdeduction = Db::name('reward')->where($where)->where('reward_status',6)->sum('member_payout');
        $membersuccess = Db::name('reward')->where($where)->where('reward_status',1)->sum('member_payout');
        $memberfailed = Db::name('reward')->where($where)->where('reward_status','>',1)->sum('member_payout');
        return success(['offers'=>$offers,'teamsuccess'=>$teamsuccess,'teamfailed' => $teamfailed,'teamdeduction'=>$teamdeduction,'membersuccess'=>$membersuccess,'memberfailed' => $memberfailed,'memberdeduction'=>$memberdeduction]);
    }
    public function ranking(){
        domain_verify();
        $param = $this->params([
            'type/s' => ''
        ]);
        $type = $param['type'];
        if($type == 'daily'){
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $where[] = ['a.create_time','between',[$start,$end]];
        }
        if($type == 'weekly'){
            $start = date('Y-m-d H:i:s', strtotime('monday this week 00:00:00'));
            $end = date('Y-m-d H:i:s');
            $where[] = ['a.create_time','between',[$start,$end]];
        }
        if($type == 'monthly'){
            $start = date('Y-m-1 00:00:00');
            $end = date('Y-m-d H:i:s');
            $where[] = ['a.create_time','between',[$start,$end]];
        }
        $where[] = ['a.team_id','=',member_team_id(true)];
        $where[] = ['a.reward_status','=',1];
        $result = Db::name('reward')->alias('a')->join('member b','a.member_id = b.member_id')->join('file c','b.avatar_id = c.file_id','LEFT')
        ->where($where)
        ->field('a.member_id,b.nickname,c.file_path as avatar_url, sum(a.member_payout) as total_member_payout, round(sum(a.member_payout/a.usd_currency_coins),2) as usd_total_member_payout,count(*) as total_member_offers')
        ->group('a.member_id')
        ->order('total_member_payout', 'desc')
        ->limit(30)
        ->select()->toArray();
        foreach($result as $key => $row){
            $result[$key]['avatar_url'] = $result[$key]['avatar_url'] ? file_url($result[$key]['avatar_url']) : file_url('/ava.png');
        }
        return success($result);
        
    }
    public function export_team_rewards()
    {
        domain_verify();
        if ($this->request->isGet()) {
            $param = $this->params(['file_path/s' => '', 'file_name/s' => '']);
            return download($param['file_path'], $param['file_name']);
        }
        $param = $this->params(['export_remark/s' => '','platform_id/s'=>'','reward_status/s'=>'','member_nickname/s'=>'']);
        $param['where'] = $this->where();
        $param['where'][] = ['team_id','=',member_team_id(true)];
        if(!empty($param['platform_id'])){
            $param['where'][] = ['platform_id','=',$param['platform_id']];
        }
        if(in_array($param['reward_status'],[1,2,3,4,5,6])){
            $param['where'][] = ['reward_status','=',$param['reward_status']];
        }
        if(!empty($param['member_nickname'])){
            $memberModel = new MemberModel();
            $member= $memberModel->where('nickname','=',$param['member_nickname'])->find();
            $param['where'][] = ['member_id','=',$member['member_id']];
        }
        $param['order'] = $this->order();
        $data = RewardService::export($param,1);
        return success($data);
    }
}