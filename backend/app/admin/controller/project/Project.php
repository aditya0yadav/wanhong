<?php

namespace app\admin\controller\project;

use app\common\controller\BaseController;
use app\common\validate\project\ProjectValidate;
use app\common\service\project\ProjectService;
use hg\apidoc\annotation as Apidoc;
use app\common\cache\project\ProjectCache;
use app\common\model\project\ProjectModel;
class Project extends BaseController
{
    
    public function list()
    {
        
        $param = $this->params(['platform_id/s' => '','search_value/s' => '','projectType/d'=>0]);
        if($param['projectType'] == 1){
            $where = $this->where();
            $where[]=['is_delete','=',1];
        } else {
            $where = $this->where(where_delete());
        }
        if($param['platform_id']){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        $data = ProjectService::list($where, $this->page(), $this->limit(), $this->order(),'platform_id,project_pno,project_no,project_name,project_code,project_click_url,project_click,project_complete,project_quota,project_loi,project_ir,project_cpi,project_icon,project_currency,project_category,project_image_url,create_time,update_time,is_disable,is_delete,delete_time,project_params,is_api',true,true);

        $data['exps'] = where_exps();
        return success($data);
    }
    public function disall(ProjectModel $ProjectModel){
        $param = $this->params(['platform_id/s' => '']);
        if($param['platform_id']){
            $ProjectModel->where('platform_id',$param['platform_id'])->update(['is_disable'=>1]);
        } else {
            $ProjectModel->update(['is_disable'=>1]);
        }
        return success();
    }
    public function delall(){
        $param = $this->params(['platform_id/s' => '']);
        if($param['platform_id']){
            $ProjectModel->where('platform_id',$param['platform_id'])->delete();
        } else {
            $ProjectModel->update(['is_disable'=>1]);
        }
        return success();
    }
    public function info()
    {
        $param = $this->params(['project_id/d' => '']);

        validate(ProjectValidate::class)->scene('info')->check($param);

        $data = ProjectService::info($param['project_id']);
        
        return success($data);
    }
    public function add()
    {
        $param = $this->params(ProjectService::$edit_field);
        $param['project_pno'] = general_pno();
        $param['project_sign'] = md5($param['platform_id'].$param['project_no'].$param['project_name'].$param['project_code']);
        validate(ProjectValidate::class)->scene('add')->check($param);
        
        $data = ProjectService::add($param);
        
        return success($data);
    }
    public function edit()
    {
        $param = $this->params(ProjectService::$edit_field);
        $param['project_sign'] = md5($param['platform_id'].$param['project_no'].$param['project_name'].$param['project_code']);
        validate(ProjectValidate::class)->scene('edit')->check($param);
        
        $data = ProjectService::edit($param['project_id'], $param);

        return success($data);
    }
    public function restore(){
        $param = $this->params(['project_id/d'=>0]);
        $data = ProjectService::restore($param['project_id']);
        return success($data);
    }
    public function clearReclycle(){
        $param = $this->params(['platform_id/d'=>0]);
        $data = ProjectService::clearReclycle($param['platform_id']);
        return success($data);
    }
    public function dele()
    {
        $param = $this->params(['ids/a' => [],'st/d'=>0]);

        validate(ProjectValidate::class)->scene('dele')->check($param);

        $data = ProjectService::dele($param['ids'],$param['st'] == 1 ?true:false);

        return success($data);
    }
    public function copy()
    {
        $param = $this->params(['ids/a' => []]);
        $data = ProjectService::copy($param['ids']);
        return success($data);
    }
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(ProjectValidate::class)->scene('disable')->check($param);
        $model = new ProjectModel();
        $pk = $model->getPk();
        $ids = $param['ids'];
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

        return success($param);
    }
}
