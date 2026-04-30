<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\platform;

use app\common\controller\BaseController;
use app\common\service\platform\PersonaService;
use app\common\service\platform\PersonaDataService;
use hg\apidoc\annotation as Apidoc;

class Persona extends BaseController
{
    public function list()
    {
        $where = $this->where();
        $data = PersonaService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        return success($data);
    }
    public function add()
    {
        $param = $this->params(['persona_name/s' => '', 'sort/d' => 0]);
        $data = PersonaService::add($param);

        return success($data);
    }
     public function info()
    {
        $param = $this->params(['persona_id/d' => '']);
        $data = PersonaService::info($param['persona_id']);
        return success($data);
    }
    public function edit()
    {
        $param = $this->params(PersonaService::$edit_field);
        $data = PersonaService::edit($param['persona_id'], $param);

        return success($data);
    }
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);
        $data = PersonaService::dele($param['ids'],true);

        return success($data);
    }
    public function dataList()
    {
        $param = $this->params(['persona_id/d' => '']);
        $where[] = ['persona_id', '=', $param['persona_id']];
        $data = PersonaDataService::list($where, $this->page(), $this->limit(), $this->order());
        return success($data);
    }
    public function dataAdd()
    {
        $param = $this->params(PersonaDataService::$edit_field);
        $data = PersonaDataService::add($param);
        return success($data);
    }
     public function dataInfo()
    {
        $param = $this->params(['persona_data_id/d' => '']);
        $data = PersonaDataService::info($param['persona_data_id']);
        return success($data);
    }
    public function dataEdit()
    {
        $param = $this->params(PersonaDataService::$edit_field);
        $data = PersonaDataService::edit($param['persona_data_id'], $param);

        return success($data);
    }
    public function dataDele()
    {
        $param = $this->params(['ids/a' => []]);
        $data = PersonaDataService::dele($param['ids'],true);
        return success($data);
    }
    public function copy()
    {
        $param = $this->params(['ids/a' => []]);
        $data = PersonaService::copy($param['ids']);
        return success($data);
    }
}
