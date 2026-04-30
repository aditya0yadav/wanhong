<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\model\project\ProjectModel;
use app\common\model\platform\PlatformModel;
use thirdsdk\Http;

class Cleardata extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('cleardata')
            ->setDescription('the clear command');
    }

    protected function execute(Input $input, Output $output)
    {
        $projectModel = new ProjectModel();
        $platformModel = new PlatformModel();
        $subDay = date('Y-m-d H:i:s', strtotime('-1 day'));
        //清理一整天无更新的api拉取项目;
        //$customIds = $platformModel->where('is_custom','=',1)->column('platform_id');
        //$projectModel->where('update_time', '<', $subDay)->where('platform_id','in',$customIds)->delete();
        //清理已删除项目;
        $projectModel->where('is_delete',1)->delete();
        echo 'clear success';
    }
}
