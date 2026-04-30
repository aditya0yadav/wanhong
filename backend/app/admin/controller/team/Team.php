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
use app\common\validate\team\TeamValidate;
use app\common\service\team\TeamService;
use app\common\service\platform\PlatformService;
use app\common\service\platform\PlatformAuthService;
use hg\apidoc\annotation as Apidoc;
use app\common\model\member\MemberModel;
use app\common\model\platform\PlatformAuthModel;
use app\common\service\team\RewardService;

/**
 * @Apidoc\Title("团队")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("300")
 */
class Team extends BaseController
{
    /**
     * @Apidoc\Title("团队列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\member\GroupModel", type="array", desc="分组列表", field="group_id,group_name,group_desc,remark,sort,is_default,is_disable,create_time,update_time")
     * @Apidoc\Returned("api", ref="app\common\model\member\ApiModel", type="tree", desc="接口树形", field="api_id,api_pid,api_name,api_url,is_unlogin,is_unauth")
     * @Apidoc\Returned(ref="app\common\model\member\GroupModel\getApiIdsAttr")
     */
    public function list()
    {
        $where = $this->where(where_delete());
        
        $data = TeamService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        return success($data);
    }
    public function teamlist()
    {
        $data = TeamService::selectList();
        return success($data);
    }
    public function teamAuth(){
        $param = $this->params(['team_id/d' => '']);
        $where[] = ['team_id','=',$param['team_id']];
        $data = PlatformAuthService::list($where, $this->page(), $this->limit(), $this->order());
        return success($data);
    }
    public function statistic(){
        $param = $this->params(['platform_id/s' => '','team_id/s'=>'','member/s'=>'','search_value/s'=>'','date_value/a'=>[]]);
        if(!$param['team_id']){
            return error('团队不存在');
        }
        $data = RewardService::customStatistic($param);
        return success($data);
    }
    /**
     * @Apidoc\Title("团队信息")
     * @Apidoc\Query(ref="app\common\model\member\GroupModel", field="group_id")
     * @Apidoc\Returned(ref="app\common\model\member\GroupModel")
     * @Apidoc\Returned(ref="app\common\model\member\GroupModel\getApiIdsAttr")
     */
    public function info()
    {
        $param = $this->params(['team_id/d' => '']);

        validate(TeamValidate::class)->scene('info')->check($param);
        
        $data = TeamService::info($param['team_id']);

        return success($data);
    }
    /**
     * @Apidoc\Title("团队添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel", field="group_name,group_desc,remark,sort")
     * @Apidoc\Param(ref="app\common\model\member\GroupModel\getApiIdsAttr", field="api_ids")
     */
    public function add()
    {
        $param = $this->params(TeamService::$edit_field);

        validate(TeamValidate::class)->scene('add')->check($param);

        $data = TeamService::add($param);

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
        $param = $this->params(TeamService::$edit_field);

        validate(TeamValidate::class)->scene('edit')->check($param);

        $data = TeamService::edit($param['team_id'], $param);

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

        validate(TeamValidate::class)->scene('dele')->check($param);
        $userModel = new MemberModel();
        $platformAuthModel = new PlatformAuthModel();
        foreach ($param['ids'] as $id) {
            $userInfo = $userModel->where('team_id',$id)->where(where_disdel())->find();
            if($userInfo){
                return error('团队删除之前需移除除该团队成员');
            }
            $platformAuthInfo = $platformAuthModel->where('team_id',$id)->find();
            if($platformAuthInfo){
                return error('团队删除之前需解除该团队已授权平台');
            }
        }
        $data = TeamService::dele($param['ids']);

        return success($data);
    }

    /**
     * @Apidoc\Title("团队是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\team\TeamModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(TeamValidate::class)->scene('disable')->check($param);

        $data = TeamService::edit($param['ids'], $param);

        return success($data);
    }
}
