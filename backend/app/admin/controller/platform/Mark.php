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
use app\common\service\platform\MarkService;
use hg\apidoc\annotation as Apidoc;

class Mark extends BaseController
{
    public function list()
    {
        $where = $this->where();
        $data = MarkService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        return success($data);
    }
    public function detailList(){
        $where = $this->where();
        $param = $this->params(['mark_id/d' => '']);
        $where[] = ['mark_id', '=', $param['mark_id']];
        $data = MarkService::detailList($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        return success($data);
    }
    public function detailDele(){
        $param = $this->params(['ids/a' => []]);

        $data = MarkService::detailDele($param['ids']);

        return success($data);
    }
    public function info()
    {
        $param = $this->params(['mark_id/d' => '']);
        $data = MarkService::info($param['mark_id']);
        return success($data);
    }
    public function add()
    {
        $param = $this->params(MarkService::$edit_field);

        $data = MarkService::add($param);

        return success($data);
    }
    
    public function edit()
    {
        $param = $this->params(MarkService::$edit_field);
        $data = MarkService::edit($param['mark_id'], $param);

        return success($data);
    }

    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        $data = MarkService::dele($param['ids']);

        return success($data);
    }
}
