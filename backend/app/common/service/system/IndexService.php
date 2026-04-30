<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\service\system;

use think\facade\Db;
use app\common\cache\Cache;
use app\common\service\system\UserService;
use app\common\model\platform\CurrencyModel;
use hg\apidoc\annotation as Apidoc;

/**
 * 控制台
 */
class IndexService
{
    /**
     * 首页
     *
     * @return array
     */
    public static function index()
    {
        $data['name']   = 'yylAdmin';
        $data['desc']   = '基于ThinkPHP8和Vue3的极简后台管理系统';
        $data['gitee']  = 'https://gitee.com/skyselang/yylAdmin';
        $data['github'] = 'https://github.com/skyselang/yylAdmin';

        return $data;
    }

    /**
     * 总数统计
     * @Apidoc\Returned("count", type="array", children={
     *   @Apidoc\Returned("name", type="string", desc="名称"),
     *   @Apidoc\Returned("count", type="int", desc="总数")
     * })
     * @return array
     */
    public static function count()
    {
        $uid  = user_id();
        $key  = 'statistic:count' . $uid . lang_get();
        //$data = Cache::get($key);
        if (empty($data)) {
            $currencyModel = new CurrencyModel();
            $currency = $currencyModel->where('currency_code','USD')->find();
            $count = [];
            $todaytemp = [];
            $todaytemp['name']  = '今天收益';
            $todaytemp['team_name']  = '团队收益';
            $todaytemp['member_name']  = '会员收益';
            $todaywhere[] = ['create_time','between',[date('Y-m-d 00:00:00'),date('Y-m-d H:i:s')]];
            $todaytemp['sydata'] = Db::name('reward')->where($todaywhere)->where('reward_status','in',[0,1])->field('ROUND(SUM(payout),2) AS all_sy,ROUND(SUM(payout / usd_currency_coins),2) AS all_sy_usd,ROUND(SUM(team_payout / usd_currency_coins),2) AS team_sy_usd,ROUND(SUM(member_payout / usd_currency_coins),2) AS member_sy_usd, ROUND(SUM(team_payout),2) AS team_sy,ROUND(SUM(member_payout),2) AS member_sy')->find();
            $todaytemp['offer_name']  = '今天参与';
            $todaytemp['offers'] = Db::name('reward')->where($todaywhere)->count();
            $todaytemp['complete_name']  = '其中完成';
            $todaytemp['complete_offers'] = Db::name('reward')->where($todaywhere)->where('reward_status','in',[0,1])->count(); 
            $count[] = $todaytemp;
            
            $weektemp = [];
            $weektemp['name']  = '本周收益';
            $weektemp['team_name']  = '团队收益';
            $weektemp['member_name']  = '会员收益';
            $weekwhere[] = ['create_time','between',[date('Y-m-d H:i:s', strtotime('monday this week 00:00:00')),date('Y-m-d H:i:s')]];
            $weektemp['sydata'] = Db::name('reward')->where($weekwhere)->where('reward_status','in',[0,1])->field('ROUND(SUM(payout),2) AS all_sy,ROUND(SUM(payout / usd_currency_coins),2) AS all_sy_usd,ROUND(SUM(team_payout / usd_currency_coins),2) AS team_sy_usd,ROUND(SUM(member_payout / usd_currency_coins),2) AS member_sy_usd, ROUND(SUM(team_payout),2) AS team_sy,ROUND(SUM(member_payout),2) AS member_sy')->find();
            $weektemp['offer_name']  = '本周参与';
            $weektemp['offers'] = Db::name('reward')->where($weekwhere)->count();
            $weektemp['complete_name']  = '其中完成';
            $weektemp['complete_offers'] = Db::name('reward')->where($weekwhere)->where('reward_status','in',[0,1])->count(); 
            $count[] = $weektemp;
            
            $monthktemp = [];
            $monthtemp['name']  = '本月收益';
            $monthtemp['team_name']  = '团队收益';
            $monthtemp['member_name']  = '会员收益';
            $monthwhere[] = ['create_time','between',[date('Y-m-01 00:00:00'),date('Y-m-d H:i:s')]];
            $monthtemp['sydata'] = Db::name('reward')->where($monthwhere)->where('reward_status','in',[0,1])->field('ROUND(SUM(payout),2) AS all_sy,ROUND(SUM(payout / usd_currency_coins),2) AS all_sy_usd,ROUND(SUM(team_payout / usd_currency_coins),2) AS team_sy_usd,ROUND(SUM(member_payout / usd_currency_coins),2) AS member_sy_usd, ROUND(SUM(team_payout),2) AS team_sy,ROUND(SUM(member_payout),2) AS member_sy')->find();
            $monthtemp['offer_name']  = '本月参与';
            $monthtemp['offers'] = Db::name('reward')->where($monthwhere)->count();
            $monthtemp['complete_name']  = '其中完成';
            $monthtemp['complete_offers'] = Db::name('reward')->where($monthwhere)->where('reward_status','in',[0,1])->count();
            $count[] = $monthtemp;
            
            $data['count'] = $count;
            Cache::set($key, $data, 60);
        }

        return $data;
    }
}
