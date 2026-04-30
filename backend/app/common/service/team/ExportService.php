<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\team;

use app\common\service\file\ExportService as FileExportService;
use app\common\model\file\ExportModel as FileExportModel;

/**
 * 业绩导出
 */
class ExportService
{
    /**
     * 导出文件保存目录
     * @var string
     */
    public static $file_dir = 'storage/export/team';
    /**
     * 导出文件保存路径
     * @return string
     */
    public static function filePath()
    {
        $public_path = public_path();
        $file_dir = $public_path . '/' . self::$file_dir;
        if (!is_dir($file_dir)) {
            mkdir($file_dir, 0777, true);
        }
        return $public_path . '/';
    }

    /**
     * 业绩导出
     * @param array $data
     * @return array|void
     */
    public static function reward($data,$type=0)
    {
        $time_start = microtime(true);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $export_id = $data['export_id'] ?? 0;
        $export_info = (new FileExportModel)->find($export_id);
        if ($export_info['status'] == FileExportService::STATUS_SUCCESS) {
            return;
        }
        if ($export_info['status'] == FileExportService::STATUS_FAIL) {
            return;
        }
        FileExportService::edit($export_id, ['status' => FileExportService::STATUS_PROCESSING]);

        $fields = [
            ['field' => 'reward_id', 'name' => 'ID', 'width' => 24],
            ['field' => 'project_pno', 'name' => 'PID', 'width' => 24],
            ['field' => 'uuid', 'name' => 'UUID', 'width' => 24],
            ['field' => 'project_no', 'name' => '项目编号', 'width' => 24],
            ['field' => 'project_name', 'name' => '项目名称', 'width' => 24],
            ['group' => 'platform','field' => 'platform_name', 'name' => '平台名称', 'width' => 24],
            ['group' => 'team','field' => 'team_name', 'name' => '团队名称', 'width' => 24],
            ['group' => 'member','field' => 'nickname', 'name' => '用户名', 'width' => 24],
            ['field' => 'front_rs', 'name' => '前置人设', 'width' => 24],
            ['field' => 'backend_rs', 'name' => '后置人设', 'width' => 24],
            ['field' => 'start_time', 'name' => '开始时间', 'width' => 24],
            ['field' => 'create_time', 'name' => '完成时间', 'width' => 24],
            ['field' => 'ip', 'name' => 'IP', 'width' => 24]
        ];
        //只导出全价奖励
        if($type == 0){
            $field='payout,';
            $fields = array_merge($fields,[
                ['field' => 'payout', 'name' => '全价奖励', 'width' => 24]
            ]);
        }
        //导出会员与团队奖励
        if($type == 1){
           $field='team_payout,member_payout,';
           $fields = array_merge($fields,[
                ['field' => 'team_payout', 'name' => '团队奖励', 'width' => 24],
                ['field' => 'member_payout', 'name' => '用户奖励', 'width' => 24]
            ]);
        }
        //导出全价，团队，会员奖励;
        if($type == 2){
           $field='payout,team_payout,member_payout,';
           $fields = array_merge($fields,[
                ['field' => 'payout', 'name' => '全价奖励', 'width' => 24],
                ['field' => 'team_payout', 'name' => '团队奖励', 'width' => 24],
                ['field' => 'member_payout', 'name' => '用户奖励', 'width' => 24]
            ]);
        }
        $fields = array_merge($fields,[['field' => 'reward_status', 'name' => '回传状态', 'width' => 24]]);
        $cell = 'A';
        $row = 2;
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($export_info['file_name']);
        foreach ($fields as $header) {
            $header_cell = $cell++;
            $sheet->setCellValue($header_cell . '1', $header['name']);
            $sheet->getColumnDimension($header_cell)->setWidth($header['width']);
        }

        $page = 1;
        $limit = 10000;
        $where = $export_info['param']['where'] ?? [];
        $order = $export_info['param']['order'] ?? [];
        $field .= 'reward_id,project_pno,project_no,project_name,platform_id,team_id,member_id,front_rs,backend_rs,create_time,start_time,reward_status,ip,uuid';
        while (true) {
            $list = RewardService::list($where, $page, $limit, $order, $field, false)['list'];
            if (empty($list)) {
                break;
            }
            foreach ($list as $v) {
                $cell = 'A';
                $rows = $row++;
                foreach ($fields as $vf) {
                    $cells = $cell++;
                    if(isset($vf['group'])){
                        $cell_val = $v[$vf['group']][$vf['field']] ?? ''; 
                    } else {
                        if($vf['field'] == 'reward_status'){
                            $cell_val = RewardService::$rewardStatus[$v[$vf['field']]] ?? '';
                        } else if($vf['field'] == 'front_rs' && $v[$vf['field']]){
                            $cell_val = '';
                            $front_rs = json_decode($v[$vf['field']],true);
                            if($front_rs && is_array($front_rs)){
                                foreach ($front_rs as $front){
                                   $cell_val.= $front['name'].':'.$front['value'].';';
                                }
                            } else {
                                $cell_val = $v[$vf['field']];
                            }
                        } else if($vf['field'] == 'backend_rs' && $v[$vf['field']]){
                            $cell_val = '';
                            $backend_rs = json_decode($v[$vf['field']],true);
                            if($backend_rs && is_array($backend_rs)){
                                foreach ($backend_rs as $backend){
                                   $cell_val.= $backend['name'].':'.$backend['value'].';';
                                }
                            } else {
                                $cell_val = $v[$vf['field']];
                            }
                        } else {
                            $cell_val = $v[$vf['field']] ?? '';
                        }
                    }
                    $sheet->setCellValue($cells . $rows, $cell_val);
                }
            }
            $page++;
        }

        $file_paths = self::filePath() . $export_info['file_path'];
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->setIncludeCharts(false);
        $writer->save($file_paths);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        
        $export_edit = [
            'file_size' => filesize($file_paths),
            'status'    => FileExportService::STATUS_SUCCESS,
            'times'     => microtime(true) - $time_start,
        ];
        FileExportService::edit($export_id, $export_edit);

        return $export_info;
    }
}
