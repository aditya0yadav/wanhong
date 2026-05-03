<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\middleware;

use Closure;
use think\Request;
use think\Response;
use app\common\service\utils\RetCodeUtils;

/**
 * 接口Token中间件
 */
class ApiTokenMiddleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // 接口是否免登
        if (!api_is_unlogin()) {

            // 临时硬编码免登白名单: survey redirect links use the token in the URL query string
            if (strpos($request->pathinfo(), 'member.Platform/link') !== false || 
                strpos($request->pathinfo(), 'member.Platform/wall_link') !== false ||
                strpos($request->pathinfo(), 'index/callback') !== false) {
                return $next($request);
            }

            // 接口token获取
            $api_token = api_token();
            if (empty($api_token)) {
                domain_verify();
                exception('请登录', RetCodeUtils::LOGIN_INVALID);
            }

            // 接口token验证
            api_token_verify($api_token);
        }

        return $next($request);
    }
}
