<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\common\controller\BaseController;
use app\api\service\IndexService;
use hg\apidoc\annotation as Apidoc;
use app\common\service\setting\SettingService;
use app\common\service\member\TokenService;
use app\common\model\persona\PersonaDataModel;
use app\common\model\persona\PersonaModel;
use app\common\service\team\RewardService;
use app\common\service\project\ProjectService;
use app\common\service\platform\PlatformService;
use app\common\model\platform\PlatformModel;
use app\common\model\platform\PlatformAuthModel;
use app\common\model\platform\FlowingModel;
use app\common\model\platform\CurrencyModel;
use app\common\model\member\MemberModel;
use app\common\model\member\AttributesModel;
use app\common\model\project\ProjectModel;
use app\common\model\team\TeamModel;
use app\common\model\team\RewardModel;
use app\common\model\file\FileModel;
use app\common\validate\file\FileValidate;
use app\common\service\file\FileService;
use app\common\service\utils\Utils;
use think\facade\View;
/**
 * @Apidoc\Title("首页")
 * @Apidoc\Group("index")
 * @Apidoc\Sort("100")
 */
class Index extends BaseController
{
    /**
     * @Apidoc\Title("首页")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    
    /**
     * @Apidoc\Title("网站信息")
     * @Apidoc\NotHeaders()
     * @Apidoc\NotQuerys()
     * @Apidoc\NotParams()
     */
    public function site(){
        $data = SettingService::info();
        $currencyModel = new CurrencyModel();
        $currency = $currencyModel->where('currency_code','USD')->find();
        $data['usd_rate'] = floatval($currency->currency_coins);
        //$team_id = member_team_id(true);
        //$teamModel = new TeamModel();
        //$teamInfo = $teamModel->with('logo')->where('team_id',$team_id)->find();
        //$data['logo_url'] = $teamInfo['logo_url'] ? $teamInfo['logo_url'] : $data['logo_url'];
        return success($data);
    }
    public function get_last_rewards(){
        $team_id = member_team_id(true);
        $where[] = ['team_id','=',$team_id];
        $where[] = ['reward_status','in',[0,1]];
        $data = RewardService::list($where, 1, 20, '','platform_id,member_id,member_payout,usd_currency_coins,project_pno,create_time');
        $fileModel = new FileModel();
        foreach ($data['list'] as $key => $row){
            $data['list'][$key]['member_name'] = $data['list'][$key]['member']['nickname'];
            $data['list'][$key]['platform_name'] = $data['list'][$key]['platform']['platform_name'];
            if($data['list'][$key]['member']['avatar_id']){
               $file = $fileModel->where('file_id',$data['list'][$key]['member']['avatar_id'])->where(where_disdel())->find();
               $data['list'][$key]['member_avatar'] = $file ? file_url($file['file_path']) : file_url('/ava.png');
            } else {
               $data['list'][$key]['member_avatar'] = file_url('/ava.png');
            }
            unset($data['list'][$key]['platform_id']);
            unset($data['list'][$key]['member']);
            unset($data['list'][$key]['platform']);
        }
        return success($data['list']);
    }
    public function topic(){
        $param = $this->params([
            'pid/s' => '',
            'platform_id/d'=>0,
            'key/s' => ''
        ]);
        $decode = TokenService::decode($param['key']);
        $member_id = $decode->data->member_id;
        if($decode && $member_id){
            $projectModel = new ProjectModel();
            $project = $projectModel->with('platform')->where('project_pno',$param['pid'])->find();
            $personaModel = new PersonaDataModel();
            if($param['platform_id']){
                $platformModel = new PlatformModel();
                $platform = $platformModel->where('platform_id',$param['platform_id'])->where(where_disdel())->find();
                $data = $personaModel->where('persona_id',$platform['platform_persona_template'])->order('sort desc')->select();
            } else {
                if($project['project_persona_template'] > 0){
                    $data = $personaModel->where('persona_id',$project['project_persona_template'])->order('sort desc')->select();
                } else {
                    $data = $personaModel->where('persona_id',$project['platform']['platform_persona_template'])->order('sort desc')->select();
                }
            }
            foreach ($data as $key => $value){
                if($data[$key]['persona_data_type'] == 'radio' || $data[$key]['persona_data_type'] == 'select'){
                    $data[$key]['persona_data_values'] = json_decode($data[$key]['persona_data_values']);
                }
            }
            return success($data);
        } else {
            return error($data);
        }
    }
    // 通用回调
    public function callback()
    {
        $param = $this->params([
            'platform/s' => '',
            'uid/s' => '',
            'status/s' => ''
        ]);
        $platformModel = new PlatformModel();
        $flowingModel = new FlowingModel();
        $platform = $platformModel->where('platform_sign', $param['platform'])->find();
        $flowing = $flowingModel->where('uuid', $param['uid'])->find();
        if ($platform && $flowing) {
            if ($platform['model_type'] == 0) {
                $currencyModel = new CurrencyModel();
                $currency = $currencyModel->where('currency_id', $platform['platform_currency'])->find();
                $memberModel = new MemberModel();
                $member = $memberModel->where('member_id', $flowing['member_id'])->find();
                $projectModel = new ProjectModel();
                $project = $projectModel->with('currency')->where('project_id', $flowing['project_id'])->find();
                $teamModel = new TeamModel();
                $team = $teamModel->where('team_id', $member['team_id'])->find();
                $rewardModel = new RewardModel();
                $reward = $rewardModel->where('txn_id', md5($param['uid']))->find();
                $platformAuthModel = new PlatformAuthModel();
                $platformAuth = $platformAuthModel->where(['platform_id' => $platform['platform_id'], 'team_id' => $member['team_id']])->find();
                
                switch ($param['status']) {
                    case 'C':
                        $cstatus = 1;
                        break;
                    case 'S':
                        $cstatus = 2;
                        break;
                    case 'Q':
                        $cstatus = 3;
                        break;
                    case 'T':
                        $cstatus = 4;
                        break;
                    case 'c':
                        $cstatus = 1;
                        break;
                    case 's':
                        $cstatus = 2;
                        break;
                    case 'q':
                        $cstatus = 3;
                        break;
                    case 't':
                        $cstatus = 4;
                        break;
                    case '1':
                        $cstatus = 1;
                        break;
                    case '2':
                        $cstatus = 2;
                        break;
                    case '3':
                        $cstatus = 3;
                        break;
                    case '4':
                        $cstatus = 4;
                        break;
                    default:
                        $cstatus = 5;
                        break;
                }
                if ($reward) {
                    $personaData = [];
                    if(!$reward['backend_rs'] && $reward['reward_status'] <=1){
                        if($project['project_persona_backend'] > 0){
                            $personaDataModel = new PersonaDataModel();
                            $personaData = $personaDataModel->where('persona_id',$project['project_persona_backend'])->order('sort desc')->select();
                            foreach ($personaData as $key => $value){
                                if($personaData[$key]['persona_data_type'] == 'radio' || $personaData[$key]['persona_data_type'] == 'select'){
                                    $personaData[$key]['persona_data_values'] = json_decode($personaData[$key]['persona_data_values']);
                                }
                            }
                        } else if($platform['platform_persona_backend'] > 0){
                            $personaDataModel = new PersonaDataModel();
                            $personaData = $personaDataModel->where('persona_id',$platform['platform_persona_backend'])->order('sort desc')->select();
                            foreach ($personaData as $key => $value){
                                if($personaData[$key]['persona_data_type'] == 'radio' || $personaData[$key]['persona_data_type'] == 'select'){
                                    $personaData[$key]['persona_data_values'] = json_decode($personaData[$key]['persona_data_values']);
                                }
                            }
                        }
                    }
                    View::assign([
                        'personaData' => $personaData,
                        'cstatus' => $reward['reward_status'],
                        'data' =>$reward
                    ]);
                    return View::fetch('index');
                }
                $isMark = 0;
                if ($platform['limit_endtime'] > 0) {
                    $checktime = time() - strtotime($flowing['create_time']);
                    if ($checktime < $platform['limit_endtime'] * 60) {
                        $isMark = 1;
                    }
                }
                $cpi = $project['project_cpi'] * $project['currency']['currency_coins'];
                // 根据当前货币汇率记录美元收益金币比例;
                if ($project['currency']['currency_code'] == 'USD') {
                    $coins = $project['currency']['currency_coins'];
                } else {
                    $usdCurrency = $currencyModel->where('currency_code', 'USD')->find();
                    $coins = $usdCurrency['currency_coins'];
                }
                if ($cstatus > 1) {
                    $data = [];
                    if ($platform['is_accept_error'] == 1) {
                        $data = [
                            'txn_id' => md5($param['uid']),
                            'member_id' => $member->member_id,
                            'team_id' => $member->team_id,
                            'platform_id' => $platform['platform_id'],
                            'payout' => round($cpi, 2),
                            'team_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100 - $platformAuth->auth_rate) / 100), 2),
                            'member_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100 - $platformAuth->auth_rate) / 100) * ((100 - $member->rate) / 100), 2),
                            'create_uid' => $member->member_id,
                            'create_time' => date('Y-m-d H:i:s'),
                            'auth_time' => date('Y-m-d H:i:s'),
                            'start_time' => $flowing['create_time'],
                            'ip' => $flowing['ip'],
                            'ua' => $flowing['ua'],
                            'project_pno' => $project['project_pno'],
                            'project_no' => $project['project_no'],
                            'project_name' => $project['project_name'],
                            'usd_currency_coins' => $coins,
                            'uuid' => $param['uid'],
                            'front_rs' => $flowing['rs_content'],
                            'address' => $flowing['country'],
                            'reward_status' => $cstatus,
                            'is_mark' => $isMark
                        ];
                        
                        $rewardModel->save($data);
                    }
                    $personaData = [];
                    View::assign([
                        'personaData' => $personaData,
                        'cstatus' => $cstatus,
                        'data' => $data
                    ]);
                    return View::fetch('index');

                } else {
                    $project->project_complete += 1;
                    $project->save();
                    $data = [
                        'txn_id' => md5($param['uid']),
                        'member_id' => $member->member_id,
                        'team_id' => $member->team_id,
                        'platform_id' => $platform['platform_id'],
                        'payout' => round($cpi, 2),
                        'team_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100 - $platformAuth->auth_rate) / 100), 2),
                        'member_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100 - $platformAuth->auth_rate) / 100) * ((100 - $member->rate) / 100), 2),
                        'create_uid' => $member->member_id,
                        'create_time' => date('Y-m-d H:i:s'),
                        'start_time' => $flowing['create_time'],
                        'ip' => $flowing['ip'],
                        'ua' => $flowing['ua'],
                        'project_pno' => $project['project_pno'],
                        'project_no' => $project['project_no'],
                        'project_name' => $project['project_name'],
                        'usd_currency_coins' => $coins,
                        'uuid' => $param['uid'],
                        'front_rs' => $flowing['rs_content'],
                        'reward_status' => $cstatus,
                        'is_mark' => $isMark
                    ];
                    $rewardModel->save($data);
                    $personaData = [];
                    if($project['project_persona_backend'] > 0){
                        $personaDataModel = new PersonaDataModel();
                        $personaData = $personaDataModel->where('persona_id',$project['project_persona_backend'])->order('sort desc')->select();
                        foreach ($personaData as $key => $value){
                            if($personaData[$key]['persona_data_type'] == 'radio' || $personaData[$key]['persona_data_type'] == 'select'){
                                $personaData[$key]['persona_data_values'] = json_decode($personaData[$key]['persona_data_values']);
                            }
                        }
                    } else if($platform['platform_persona_backend'] > 0){
                        $personaDataModel = new PersonaDataModel();
                        $personaData = $personaDataModel->where('persona_id',$platform['platform_persona_backend'])->order('sort desc')->select();
                        foreach ($personaData as $key => $value){
                            if($personaData[$key]['persona_data_type'] == 'radio' || $personaData[$key]['persona_data_type'] == 'select'){
                                $personaData[$key]['persona_data_values'] = json_decode($personaData[$key]['persona_data_values']);
                            }
                        }
                    }
                    View::assign([
                        'personaData' => $personaData,
                        'cstatus' => $cstatus,
                        'data' => $data
                    ]);
                    return View::fetch('index');
                }
            } else {
                
            }
            
        }
        //return success();
    }
    // 通用回调
    public function index()
    {
        $param = $this->params([
            'platform/s' => '',
            'uid/s' => '',
            'status/s' => ''
        ]);
        $platformModel = new PlatformModel();
        $flowingModel = new FlowingModel();
        $platform = $platformModel->where('platform_sign', $param['platform'])->find();
        $flowing = $flowingModel->where('uuid', $param['uid'])->find();
        if ($platform && $flowing) {
            if ($platform['model_type'] == 0) {
                $currencyModel = new CurrencyModel();
                $currency = $currencyModel->where('currency_id', $platform['platform_currency'])->find();
                $memberModel = new MemberModel();
                $member = $memberModel->where('member_id', $flowing['member_id'])->find();
                $projectModel = new ProjectModel();
                $project = $projectModel->with('currency')->where('project_id', $flowing['project_id'])->find();
                $teamModel = new TeamModel();
                $team = $teamModel->where('team_id', $member['team_id'])->find();
                $rewardModel = new RewardModel();
                $reward = $rewardModel->where('txn_id', md5($param['uid']))->find();
                $platformAuthModel = new PlatformAuthModel();
                $platformAuth = $platformAuthModel->where(['platform_id' => $platform['platform_id'], 'team_id' => $member['team_id']])->find();
                
                switch ($param['status']) {
                    case 'C':
                        $cstatus = 1;
                        break;
                    case 'S':
                        $cstatus = 2;
                        break;
                    case 'Q':
                        $cstatus = 3;
                        break;
                    case 'T':
                        $cstatus = 4;
                        break;
                    case 'c':
                        $cstatus = 1;
                        break;
                    case 's':
                        $cstatus = 2;
                        break;
                    case 'q':
                        $cstatus = 3;
                        break;
                    case 't':
                        $cstatus = 4;
                        break;
                    case '1':
                        $cstatus = 1;
                        break;
                    case '2':
                        $cstatus = 2;
                        break;
                    case '3':
                        $cstatus = 3;
                        break;
                    case '4':
                        $cstatus = 4;
                        break;
                    default:
                        $cstatus = 5;
                        break;
                }
                if ($reward) {
                    $personaData = [];
                    if(!$reward['backend_rs'] && $reward['reward_status'] <=1){
                        if($project['project_persona_backend'] > 0){
                            $personaDataModel = new PersonaDataModel();
                            $personaData = $personaDataModel->where('persona_id',$project['project_persona_backend'])->order('sort desc')->select();
                            foreach ($personaData as $key => $value){
                                if($personaData[$key]['persona_data_type'] == 'radio' || $personaData[$key]['persona_data_type'] == 'select'){
                                    $personaData[$key]['persona_data_values'] = json_decode($personaData[$key]['persona_data_values']);
                                }
                            }
                        } else if($platform['platform_persona_backend'] > 0){
                            $personaDataModel = new PersonaDataModel();
                            $personaData = $personaDataModel->where('persona_id',$platform['platform_persona_backend'])->order('sort desc')->select();
                            foreach ($personaData as $key => $value){
                                if($personaData[$key]['persona_data_type'] == 'radio' || $personaData[$key]['persona_data_type'] == 'select'){
                                    $personaData[$key]['persona_data_values'] = json_decode($personaData[$key]['persona_data_values']);
                                }
                            }
                        }
                    }
                    View::assign([
                        'personaData' => $personaData,
                        'cstatus' => $reward['reward_status'],
                        'data' =>$reward
                    ]);
                    return View::fetch('index');
                }
                $isMark = 0;
                if ($platform['limit_endtime'] > 0) {
                    $checktime = time() - strtotime($flowing['create_time']);
                    if ($checktime < $platform['limit_endtime'] * 60) {
                        $isMark = 1;
                    }
                }
                $cpi = $project['project_cpi'] * $project['currency']['currency_coins'];
                // 根据当前货币汇率记录美元收益金币比例;
                if ($project['currency']['currency_code'] == 'USD') {
                    $coins = $project['currency']['currency_coins'];
                } else {
                    $usdCurrency = $currencyModel->where('currency_code', 'USD')->find();
                    $coins = $usdCurrency['currency_coins'];
                }
                if ($cstatus > 1) {
                    $data = [];
                    if ($platform['is_accept_error'] == 1) {
                        $data = [
                            'txn_id' => md5($param['uid']),
                            'member_id' => $member->member_id,
                            'team_id' => $member->team_id,
                            'platform_id' => $platform['platform_id'],
                            'payout' => round($cpi, 2),
                            'team_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100 - $platformAuth->auth_rate) / 100), 2),
                            'member_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100 - $platformAuth->auth_rate) / 100) * ((100 - $member->rate) / 100), 2),
                            'create_uid' => $member->member_id,
                            'create_time' => date('Y-m-d H:i:s'),
                            'auth_time' => date('Y-m-d H:i:s'),
                            'start_time' => $flowing['create_time'],
                            'ip' => $flowing['ip'],
                            'ua' => $flowing['ua'],
                            'project_pno' => $project['project_pno'],
                            'project_no' => $project['project_no'],
                            'project_name' => $project['project_name'],
                            'usd_currency_coins' => $coins,
                            'uuid' => $param['uid'],
                            'front_rs' => $flowing['rs_content'],
                            'address' => $flowing['country'],
                            'reward_status' => $cstatus,
                            'is_mark' => $isMark
                        ];
                        
                        $rewardModel->save($data);
                    }
                    $personaData = [];
                    View::assign([
                        'personaData' => $personaData,
                        'cstatus' => $cstatus,
                        'data' => $data
                    ]);
                    return View::fetch('index');

                } else {
                    $project->project_complete += 1;
                    $project->save();
                    $data = [
                        'txn_id' => md5($param['uid']),
                        'member_id' => $member->member_id,
                        'team_id' => $member->team_id,
                        'platform_id' => $platform['platform_id'],
                        'payout' => round($cpi, 2),
                        'team_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100 - $platformAuth->auth_rate) / 100), 2),
                        'member_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100 - $platformAuth->auth_rate) / 100) * ((100 - $member->rate) / 100), 2),
                        'create_uid' => $member->member_id,
                        'create_time' => date('Y-m-d H:i:s'),
                        'start_time' => $flowing['create_time'],
                        'ip' => $flowing['ip'],
                        'ua' => $flowing['ua'],
                        'project_pno' => $project['project_pno'],
                        'project_no' => $project['project_no'],
                        'project_name' => $project['project_name'],
                        'usd_currency_coins' => $coins,
                        'uuid' => $param['uid'],
                        'front_rs' => $flowing['rs_content'],
                        'reward_status' => $cstatus,
                        'is_mark' => $isMark
                    ];
                    $rewardModel->save($data);
                    $personaData = [];
                    if($project['project_persona_backend'] > 0){
                        $personaDataModel = new PersonaDataModel();
                        $personaData = $personaDataModel->where('persona_id',$project['project_persona_backend'])->order('sort desc')->select();
                        foreach ($personaData as $key => $value){
                            if($personaData[$key]['persona_data_type'] == 'radio' || $personaData[$key]['persona_data_type'] == 'select'){
                                $personaData[$key]['persona_data_values'] = json_decode($personaData[$key]['persona_data_values']);
                            }
                        }
                    } else if($platform['platform_persona_backend'] > 0){
                        $personaDataModel = new PersonaDataModel();
                        $personaData = $personaDataModel->where('persona_id',$platform['platform_persona_backend'])->order('sort desc')->select();
                        foreach ($personaData as $key => $value){
                            if($personaData[$key]['persona_data_type'] == 'radio' || $personaData[$key]['persona_data_type'] == 'select'){
                                $personaData[$key]['persona_data_values'] = json_decode($personaData[$key]['persona_data_values']);
                            }
                        }
                    }
                    View::assign([
                        'personaData' => $personaData,
                        'cstatus' => $cstatus,
                        'data' => $data
                    ]);
                    return View::fetch('index');
                }
            } else {
                
            }
            
        }
        //return success();
    }
    public function upload(){
        $param = $this->params([
            'txn_id/s' => '',
        ]);
        $rewardModel = new RewardModel();
        $reward = $rewardModel->where('txn_id', $param['txn_id'])->find();
        if($reward && !$reward['backend_rs'] && $reward['reward_status'] <=1){
            $fileParmas['file'] = $this->request->file('image');
            $fileParmas['is_front']= 1;
            validate(FileValidate::class)->scene('add')->check($fileParmas);
            $data = FileService::add($fileParmas);
            return success(['url'=>$data['file_url']]);
        } else {
            return error('业绩记录异常，已提交或不存在');
        }
    }
    public function rs(){
        $param = $this->params([
            'txn_id/s' => '',
            'anser/a' => []
        ]);
        $rewardModel = new RewardModel();
        $reward = $rewardModel->where('txn_id', $param['txn_id'])->find();
        if($reward && !$reward['backend_rs'] && $reward['reward_status'] <=1){
             $projectModel = new ProjectModel();
             if($reward['project_pno']){
                $project = $projectModel->where('project_pno',$reward['project_pno'])->where(where_disdel())->find();
             }
             $platformModel = new PlatformModel();
             $platform = $platformModel->where('platform_id',$reward['platform_id'])->where(where_disdel())->find();
            if(isset($project) && $project['project_persona_backend'] > 0){
                $PersonaModel = new PersonaModel();
                $persona = $PersonaModel->where('persona_id',$project['project_persona_backend'])->find();
                if(empty($param['anser'])){
                    return error('No personnel information filled in');
                }
                $PersonaDataModel = new PersonaDataModel();
                $personaData = $PersonaDataModel->where('persona_id',$project['project_persona_backend'])->select()->toArray();
                foreach($personaData as $row){
                    if(isset($param['anser'][$row['persona_data_id']])){
                        if($row['persona_data_must'] == 1 && !$param['anser'][$row['persona_data_id']]){
                            return error($row['persona_data_holder']);
                        }
                        if(($row['persona_data_type'] == 'radio'|| $row['persona_data_type'] == 'select') && $param['anser'][$row['persona_data_id']]){
                            $values = json_decode($row['persona_data_values'],true);
                            foreach ($values as $v){
                                $check[] = $v['value'];
                            }
                            if(!in_array($param['anser'][$row['persona_data_id']],$check)){
                                return error($row['persona_data_name'].' error');
                            }
                        }
                        if($row['persona_data_type'] == 'input' && $param['anser'][$row['persona_data_id']]){
                            if(strlen($param['anser'][$row['persona_data_id']]) > 2000){
                                return error('Exceeding the word limit');
                            }
                            
                        }
                        if($row['persona_data_type'] == 'date' && $param['anser'][$row['persona_data_id']]){
                            if(!preg_match('/^\d{10,13}$/', $param['anser'][$row['persona_data_id']])){
                                return error('Date is illegal');
                            }
                            $param['anser'][$row['persona_data_id']] = date('Y-m-d',$param['anser'][$row['persona_data_id']]);
                        }
                        $anser[] = ['name'=> $row['persona_data_name'],'value'=> $param['anser'][$row['persona_data_id']]];
                    } else {
                        return error('illegal request');
                    }
                }
            } else if($platform['platform_persona_backend'] > 0){
                $PersonaModel = new PersonaModel();
                $persona = $PersonaModel->where('persona_id',$platform['platform_persona_backend'])->find();
                if($persona['persona_type'] == 1){
                    if(empty($param['anser'])){
                        return error('No personnel information filled in');
                    }
                    $PersonaDataModel = new PersonaDataModel();
                    $personaData = $PersonaDataModel->where('persona_id',$platform['platform_persona_backend'])->select()->toArray();
                    foreach($personaData as $row){
                        if(isset($param['anser'][$row['persona_data_id']])){
                            if($row['persona_data_must'] == 1 && !$param['anser'][$row['persona_data_id']]){
                                return error($row['persona_data_holder']);
                            }
                            if(($row['persona_data_type'] == 'radio'|| $row['persona_data_type'] == 'select') && $param['anser'][$row['persona_data_id']]){
                                $values = json_decode($row['persona_data_values'],true);
                                foreach ($values as $v){
                                    $check[] = $v['value'];
                                }
                                if(!in_array($param['anser'][$row['persona_data_id']],$check)){
                                    return error($row['persona_data_name'].' error');
                                }
                            }
                            if($row['persona_data_type'] == 'input' && $param['anser'][$row['persona_data_id']]){
                                if(strlen($param['anser'][$row['persona_data_id']]) > 2000){
                                    return error('Exceeding the word limit');
                                }
                                
                            }
                            if($row['persona_data_type'] == 'date' && $param['anser'][$row['persona_data_id']]){
                                if(!preg_match('/^\d{10,13}$/', $param['anser'][$row['persona_data_id']])){
                                    return error('Date is illegal');
                                }
                            }
                            $anser[] = ['name'=> $row['persona_data_name'],'type'=>$row['persona_data_type'],'value'=> $param['anser'][$row['persona_data_id']]];
                        } else {
                            return error('illegal request');
                        }
                    }
                }
            } else {
                return error('该项目或平台无人设模版');
            }
            $reward->backend_rs = json_encode($anser);
            $reward->save();
            return success('编辑成功');
        } else {
            return error('未查询到回调流水记录');
        }
    }
    // Bitlabs回调
    public function bitlabs(){
        $get    = $this->request->get();
        file_put_contents(__DIR__.'/bitlabs_callback.txt',__FUNCTION__.':'.date('Y-m-d H:i:s')."\n",8);
        file_put_contents(__DIR__.'/bitlabs_callback.txt',json_encode($get,320)."\n\n",8);
        $uuid = $this->request->get('uid');
        $status = $this->request->get('status');
        $offer_id = strtoupper($this->request->get('offer_id'));
        $offer_name = strtoupper($this->request->get('offer_name'));
        $cpi = $this->request->get('cpi');
        $tx = $this->request->get('tx');
        /*检查是否有重复交易*/
        $rewardModel = new RewardModel();
        $reward = $rewardModel->where('txn_id',md5($uuid))->find();
        if ($reward) {
            if($status == 'RECONCILIATION'){
                $reward->reward_status = 1;
                $reward->save();
            }
            if($status == 'SCREENOUT'){
                $reward->reward_status = 2;
                $reward->save();
            }
            return 1;
        }
        $platformModel = new PlatformModel();
        $flowingModel = new FlowingModel();
        $platform = $platformModel->with('currency')->where('platform_sign','Bitlabs')->find();
        $flowing = $flowingModel->where('uuid',$uuid)->find();
        $currencyModel = new CurrencyModel();
        $currency = $currencyModel->where('currency_id',$platform['platform_currency'])->find();
        $memberModel = new MemberModel();
        $member = $memberModel->where('member_id',$flowing['member_id'])->find();
        $teamModel = new TeamModel();
        $team = $teamModel->where('team_id',$member['team_id'])->find();
        $platformAuthModel = new PlatformAuthModel();
        $platformAuth = $platformAuthModel->where(['platform_id'=> $platform['platform_id'],'team_id' => $member['team_id'] ])->find();
        if ($status == 'COMPLETE') {
            $cpi = $cpi * $platform['currency']['currency_coins'];
            $cstatus = 1;
            // 根据当前货币汇率记录美元收益金币比例;
            if($platform['currency']['currency_code'] == 'USD'){
                $coins = $platform['currency']['currency_coins'];
            } else {
                $usdCurrency = $currencyModel->where('currency_code','USD')->find();
                $coins = $usdCurrency['currency_coins'];
            }
            $data = [
                'txn_id' => md5($uuid),
                'member_id' => $member->member_id,
                'team_id' => $member->team_id,
                'platform_id' => $platform['platform_id'],
                'payout' => round($cpi,2),
                'team_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100-$platformAuth->auth_rate) / 100),2),
                'member_payout' => round($cpi * ((100 - $team->commission_ratio) / 100) * ((100-$platformAuth->auth_rate) / 100) * ((100 - $member->rate) / 100),2),
                'create_uid' => $member->member_id,
                'create_time' => date('Y-m-d H:i:s'),
                'start_time' => $flowing['create_time'],
                'ip' => $flowing['ip'],
                'ua' => $flowing['ua'],
                'project_pno' => '',
                'project_no' => $offer_id,
                'project_name' => $offer_name,
                'usd_currency_coins' => $coins,
                'uuid' => $uuid,
                'rs' => $flowing['rs_content'],
                'reward_status' => $cstatus
            ];
            $rewardModel->save($data);
        };
    }
}
