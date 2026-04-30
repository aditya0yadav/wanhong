<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\admin\controller\system;

use app\common\controller\BaseController;
use app\common\service\system\IndexService;
use app\common\service\system\NoticeService;
use app\common\service\member\MemberService;
use app\common\service\team\RewardService;
use app\common\service\content\ContentService;
use app\common\service\file\FileService;
use hg\apidoc\annotation as Apidoc;
use app\common\model\platform\PlatformModel;
use app\common\model\team\RewardModel;
use app\common\model\team\TeamModel;
/**
 * @Apidoc\Title("控制台")
 * @Apidoc\Group("console")
 * @Apidoc\Sort("150")
 */
class Index extends BaseController
{
    /**
     * @Apidoc\Title("首页")
     */
    public function index()
    {
        $data = IndexService::index();
        $msg  = '后端安装成功，欢迎使用，如有帮助，敬请Star！';

        return success($data, $msg);
    }

    /**
     * @Apidoc\Title("公告")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", type="array", desc="公告列表", children={
     *   @Apidoc\Returned(ref="app\common\model\system\NoticeModel", field="notice_id,image_id,title,title_color,desc,start_time"),
     *   @Apidoc\Returned(ref="app\common\model\system\NoticeModel\getImageUrlAttr", field="image_url")
     * })
     */
    public function notice()
    {
        $where[] = ['start_time', '<=', datetime()];
        $where[] = ['end_time', '>=', datetime()];
        $where[] = where_disable();
        $where[] = where_delete();
        $where = $this->where($where);

        $order = ['sort' => 'desc', 'start_time' => 'desc', 'notice_id' => 'desc'];

        $field = 'image_id,title,title_color,desc,start_time';

        $data = NoticeService::list($where, $this->page(), $this->limit(), $this->order($order), $field);

        return success($data);
    }

    /**
     * @Apidoc\Title("总数统计")
     * @Apidoc\Returned(ref="app\common\service\system\IndexService\count")
     */
    public function count()
    {
        $data = IndexService::count();

        return success($data);
    }

    /**
     * @Apidoc\Title("会员统计")
     * @Apidoc\Query("type", type="string", default="month", desc="日期类型：day、month")
     * @Apidoc\Query("date", type="array", default="", desc="日期范围，默认30天、12个月")
     * @Apidoc\Returned("number", type="array", desc="图表数据", children={
     *   @Apidoc\Returned(ref="app\common\service\member\MemberService\statistic")
     * })
     */
    public function member()
    {
        $type = $this->param('type/s', '');
        $date = $this->param('date/a', []);

        $data['number'] = MemberService::statistic($type, $date, 'number');

        return success($data);
    }
    public function reward(){
        $type = $this->param('type/s', '');
        $date = $this->param('date/a', []);
        $data = RewardService::statistic($type, $date);
        return success($data);
    }
    public function statistic(){
        $param = $this->params(['platform_id/s' => '','team_id/s'=>'','member/s'=>'','search_value/s'=>'','date_value/a'=>[]]);
        $data = RewardService::customStatistic($param);
        return success($data);
    }
    //平台每日业绩排名
    public function platformrank()
{
    $date = $this->param('date/s', date('Y-m-d'));

    $start = $date . ' 00:00:00';
    $end   = $date . ' 23:59:59';

    $list = RewardModel::alias('r')
        ->field([
            'p.platform_id',
            'p.platform_name',
            'SUM(r.payout/ NULLIF(r.usd_currency_coins, 0)) as total_amount',
            'SUM(r.team_payout/ NULLIF(r.usd_currency_coins, 0)) as total_team_amount',
            'SUM(r.member_payout/ NULLIF(r.usd_currency_coins, 0)) as total_member_amount'
            
        ])
        ->join(PlatformModel::getTable() . ' p', 'p.platform_id = r.platform_id')
        ->where('r.reward_status','in', [0,1])
        ->whereBetween('r.create_time', [$start, $end])
        ->group('r.platform_id')
        ->order('total_amount desc')
        ->select()
        ->toArray();
    // 添加排名
    foreach ($list as $k => &$v) {
        $v['rank'] = $k + 1;
    }

    return success([
        'date' => $date,
        'list' => $list
    ]);
}
    //团队每日业绩排名
    public function teamrank()
{
    $date = $this->param('date/s', date('Y-m-d'));

    $start = $date . ' 00:00:00';
    $end   = $date . ' 23:59:59';

    $list = RewardModel::alias('r')
        ->field([
            't.team_id',
            't.team_name',
            'SUM(r.payout / NULLIF(r.usd_currency_coins, 0)) as total_amount',
            'SUM(r.team_payout/ NULLIF(r.usd_currency_coins, 0)) as total_team_amount',
            'SUM(r.member_payout/ NULLIF(r.usd_currency_coins, 0)) as total_member_amount'
        ])
        ->join(TeamModel::getTable() . ' t', 't.team_id = r.team_id')
        ->where('r.reward_status','in', [0,1])
        ->whereBetween('r.create_time', [$start, $end])
        ->group('r.team_id')
        ->order('total_amount desc')
        ->select()
        ->toArray();

    foreach ($list as $k => &$v) {
        $v['rank'] = $k + 1;
    }

    return success([
        'date' => $date,
        'list' => $list
    ]);
}
 // 团队业绩排名（成功业绩 - 核减业绩，不限制日期）
public function rewardrank()
{
    $list = RewardModel::alias('r')
        ->field([
            't.team_id',
            't.team_name',

            // 团队成功业绩
            "SUM(
                CASE 
                    WHEN r.reward_status = 1 
                    THEN r.team_payout / NULLIF(r.usd_currency_coins,0)
                    ELSE 0
                END
            ) as success_team_amount",

            // 团队核减业绩
            "SUM(
                CASE 
                    WHEN r.reward_status = 6 
                    THEN r.team_payout / NULLIF(r.usd_currency_coins,0)
                    ELSE 0
                END
            ) as reduce_team_amount",
        ])
        ->join(TeamModel::getTable() . ' t', 't.team_id = r.team_id')
        ->whereIn('r.reward_status', [1,6])
        ->group('r.team_id')
        ->order('success_team_amount desc')
        ->select()
        ->toArray();

    // 排名
    foreach ($list as $k => &$v) {
        $v['rank'] = $k + 1;
    }

    return success([
        'list' => $list
    ]);
}

    /**
     * @Apidoc\Title("内容统计")
     * @Apidoc\Returned(ref="app\common\service\content\ContentService\statistic")
     */
    public function content()
    {
        $data = ContentService::statistic();
        return success($data);
    }

    /**
     * @Apidoc\Title("文件统计")
     * @Apidoc\Returned(ref="app\common\service\file\FileService\statistic")
     */
    public function file()
    {
        $data = FileService::statistic();

        return success($data);
    }
}
