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
use app\common\service\platform\TopicService;
use hg\apidoc\annotation as Apidoc;

class Topic extends BaseController
{
    public function list()
    {
        $where = $this->where();
        $param = $this->params(['platform_id/s' => '']);
        if($param['platform_id']){
            $where[] = ['platform_id','=',$param['platform_id']];
        }
        $data = TopicService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        return success($data);
    }
    public function info()
    {
        $param = $this->params(['topic_id/d' => '']);
        $data = TopicService::info($param['topic_id']);
        return success($data);
    }
    public function add()
    {
        $param = $this->params(TopicService::$edit_field);

        $data = TopicService::add($param);

        return success($data);
    }
    
    public function edit()
    {
        $param = $this->params(TopicService::$edit_field);
        $data = TopicService::edit($param['topic_id'], $param);

        return success($data);
    }

    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        $data = TopicService::dele($param['ids']);

        return success($data);
    }
}
