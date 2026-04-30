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
use app\common\validate\platform\PlatformValidate;
use app\common\service\platform\PlatformService;
use app\common\validate\platform\PlatformAuthValidate;
use app\common\service\platform\PlatformAuthService;
use hg\apidoc\annotation as Apidoc;
use app\common\model\platform\CurrencyModel;
use app\common\model\project\ProjectModel;
use app\common\service\utils\Utils;
use thirdsdk\Http;
use think\facade\Console;
use think\facade\Queue;
use app\job\PullJob;
/**
 * @Apidoc\Title("平台管理")
 * @Apidoc\Group("platform")
 * @Apidoc\Sort("300")
 */
class Platform extends BaseController
{
    /**
     * @Apidoc\Title("平台列表")
     * @Apidoc\Query(ref="pagingQuery")
     * @Apidoc\Query(ref="sortQuery")
     * @Apidoc\Query(ref="searchQuery")
     * @Apidoc\Query(ref="dateQuery")
     * @Apidoc\Returned(ref="expsReturn")
     * @Apidoc\Returned(ref="pagingReturn")
     * @Apidoc\Returned("list", ref="app\common\model\platform\PlatformModel", type="array", desc="平台列表", field="platform_id,platform_name,platform_sign,is_default,is_disable,create_time,update_time")
     */
    public function list()
    {
        $where = $this->where(where_delete());
        $data = PlatformService::list($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        return success($data);
    }
    public function currencyList()
    {
        $where = $this->where();
        $data = PlatformService::currencyList($where, $this->page(), $this->limit(), $this->order());
        $data['exps'] = where_exps();
        return success($data);
    }
    public function currencyAdd()
    {
        $param = $this->params(['currency_name/s' => '', 'currency_code/s' => '', 'currency_coins/s' => '']);
        $data = PlatformService::currencyAdd($param);

        return success($data);
    }
    public function currencyEdit()
    {
        $param = $this->params(['currency_id/d' => '', 'currency_name/s' => '', 'currency_code/s' => '', 'currency_coins/s' => '']);

        $data = PlatformService::currencyEdit($param['currency_id'], $param);

        return success($data);
    }
    public function currencyDele()
    {
        $param = $this->params(['currency_id/d' => '']);
        $data = PlatformService::currencyDele($param['currency_id']);

        return success($data);
    }
    public function platlist()
    {
        $data = PlatformService::selectList();
        return success($data);
    }
    public function currencyAllList()
    {
        $data = PlatformService::currencyAllList();
        return success($data);
    }
    public function platformAuthList()
    {
        $param = $this->params(['platform_id/d' => '']);
        $where[] = ['platform_id', '=', $param['platform_id']];
        $data = PlatformAuthService::list($where, $this->page(), $this->limit(), $this->order());
        return success($data);
    }
    public function platformAuth()
    {
        $param = $this->params(PlatformAuthService::$edit_field);
        validate(PlatformAuthValidate::class)->scene('add')->check($param);
        $data = PlatformAuthService::add($param);
        return success($data);
    }
    public function platformEditAuth()
    {
        $param = $this->params(PlatformAuthService::$edit_field);
        validate(PlatformAuthValidate::class)->scene('edit')->check($param);
        $data = PlatformAuthService::edit($param['platform_auth_id'],$param);
        return success($data);
    }
    public function deleAuth()
    {
        $param = $this->params(['platform_auth_id/d' => '']);

        validate(PlatformAuthValidate::class)->scene('dele')->check($param);

        $data = PlatformAuthService::dele($param['platform_auth_id']);

        return success($data);
    }
    /**
     * @Apidoc\Title("平台信息")
     * @Apidoc\Query(ref="app\common\model\platform\PlatformModel", field="platform_id")
     * @Apidoc\Returned(ref="app\common\model\platform\PlatformModel")
     */
    public function info()
    {
        $param = $this->params(['platform_id/d' => '']);

        validate(PlatformValidate::class)->scene('info')->check($param);

        $data = PlatformService::info($param['platform_id']);
        return success($data);
    }

    /**
     * @Apidoc\Title("平台添加")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\platform\PlatformModel", field="platform_name,platform_sign,platform_image,sort")
     */
    public function add()
    {
        $param = $this->params(PlatformService::$edit_field);

        validate(PlatformValidate::class)->scene('add')->check($param);

        $data = PlatformService::add($param);

        return success($data);
    }
    public function platformStatistic(){
        $param = $this->params(['platform_id/d' => '','date_value/a'=>[]]);
        $data = PlatformService::statistic($param['platform_id'],$param);
        return success($data);
    }
    /**
     * @Apidoc\Title("平台修改")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="app\common\model\platform\PlatformModel", field="platform_id,platform_name,platform_sign,platform_image,sort")
     */
    public function edit()
    {
        $param = $this->params(PlatformService::$edit_field);

        validate(PlatformValidate::class)->scene('edit')->check($param);

        $data = PlatformService::edit($param['platform_id'], $param);

        return success($data);
    }

    /**
     * @Apidoc\Title("平台删除")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     */
    public function dele()
    {
        $param = $this->params(['ids/a' => []]);

        validate(PlatformValidate::class)->scene('dele')->check($param);

        $data = PlatformService::dele($param['ids'],true);

        return success($data);
    }

    /**
     * @Apidoc\Title("平台是否禁用")
     * @Apidoc\Method("POST")
     * @Apidoc\Param(ref="idsParam")
     * @Apidoc\Param(ref="app\common\model\platform\PlatformModel", field="is_disable")
     */
    public function disable()
    {
        $param = $this->params(['ids/a' => [], 'is_disable/d' => 0]);

        validate(PlatformValidate::class)->scene('disable')->check($param);

        $data = PlatformService::edit($param['ids'], $param);

        return success($data);
    }
    public function pull()
    {
        $param = $this->params(['platform_id/d' => '']);
        $platform = PlatformService::info($param['platform_id']);
        if($platform){
            //Queue::push('app\job\PullJob@fire',['platform_sign' =>$platform['platform_sign'],'user_id'=>user_id()], 'sycPullJob');
            Console::call('pull', ['type' => $platform['platform_sign']]);
            return success('执行成功');
        } else {
            return error('执行失败');
        }

    }
}
