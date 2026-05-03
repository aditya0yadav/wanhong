<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\api\controller\member;

use think\facade\Validate;
use app\common\controller\BaseController;
use hg\apidoc\annotation as Apidoc;
use app\common\model\platform\PlatformAuthModel;
use app\common\model\platform\PlatformModel;
use app\common\model\platform\FlowingModel;
use app\common\model\platform\MarkModel;
use app\common\model\platform\MarkDetailModel;
use app\common\model\project\ProjectModel;
use app\common\service\member\MemberService;
use app\common\service\project\ProjectService;
use app\common\service\platform\PlatformService;
use app\common\service\team\TeamService;
use app\common\service\team\RewardService;
use app\common\service\utils\Utils;
use app\common\service\member\TokenService;
use app\common\model\topic\TopicModel;
use app\common\model\team\RewardModel;

use app\common\model\persona\PersonaModel;
use app\common\model\persona\PersonaDataModel;
use think\facade\Db;
/**
 * @Apidoc\Title("平台")
 * @Apidoc\Group("member")
 * @Apidoc\Sort("300")
 */
class Platform extends BaseController
{
	/**
	 * @Apidoc\Title("平台列表")
	 * @Apidoc\Method("GET")
	 */
	public function list()
	{
		domain_verify();
		$teamid = member_team_id(true);
		$platformAuthModel = new PlatformAuthModel();
		$authIds = $platformAuthModel->where('team_id', $teamid)->column('platform_id');
		$platformModel = new PlatformModel();
		$data = $platformModel->with(['logo'])->field(['platform_id', 'platform_name', 'platform_image', 'platform_color', 'is_list', 'is_wall'])->whereIn('platform_id', $authIds)->where(where_disdel())->select()->append(['logo_url'])
			->hidden(['logo'])->toArray();
		return success($data);
	}
	public function offers()
	{
		domain_verify();
		$param = $this->params([
			'platform_id/d' => 0,
			'code/s' => '',
			'project_pno/s' => '',
			'project_name/s' => ''
		]);
		if (!empty($param['code'])) {
			$where[] = ['project_code', 'like', '%' . $param['code'] . '%'];
		}
		if (!empty($param['project_pno'])) {
			$where[] = ['project_pno', 'like', '%' . $param['project_pno'] . '%'];
		}
		if (!empty($param['project_name'])) {
			$where['project_name'] = ['project_name', '=', $param['project_name']];
		}
		$where[] = ['platform_id', '=', $param['platform_id']];
		$where = where_disdel($where);
		$data = ProjectService::list($where, $this->page(), $this->limit(), $this->order(), 'project_pno,project_name,project_code,project_cpi,project_currency,project_loi,project_ir,create_time,update_time,sort,platform_id,project_params,project_quota,project_complete');
		$member_id = member_id(true);
		$team_id = member_team_id(true);
		$member = MemberService::info($member_id, true, true, true);
		$team = TeamService::info($team_id, true, true, true);
		$platform = PlatformService::info($param['platform_id'], true, true, true);
		$data['show_quota'] = (int) $platform['show_quota'];
		$data['show_name'] = (int) $platform['show_name'];
		$data['show_loi'] = (int) $platform['show_loi'];
		$data['show_ir'] = (int) $platform['show_ir'];
		$platformAuthModel = new PlatformAuthModel();
		$platformAuth = $platformAuthModel->where(['platform_id' => $param['platform_id'], 'team_id' => member_team_id(true)])->find();
		foreach ($data['list'] as $key => $row) {
			if ($row['platform']['show_quota'] != 1) {
				unset($data['list'][$key]['project_quota']);
			}
			if ($row['platform']['show_click'] != 1) {
				unset($data['list'][$key]['project_click']);
			}
			if ($row['platform']['show_complete'] != 1) {
				unset($data['list'][$key]['project_complete']);
			}
			if ($row['platform']['show_no'] != 1) {
				unset($data['list'][$key]['project_no']);
			}
			if ($row['platform']['show_loi'] != 1) {
				unset($data['list'][$key]['project_loi']);
			}
			if ($row['platform']['show_ir'] != 1) {
				unset($data['list'][$key]['project_ir']);
			}
			$data['list'][$key]['project_cpi'] = round($data['list'][$key]['project_cpi'] * $data['list'][$key]['currency']['currency_coins'] * (100 - $team['commission_ratio']) / 100 * (100 - $member['rate']) / 100 * (100 - $platformAuth['auth_rate']) / 100, 2);
			$data['list'][$key]['project_params'] = $data['list'][$key]['project_params'] == 'null' ? null : $data['list'][$key]['project_params'];
		}
		return success($data);
	}
	public function featured()
	{
		domain_verify();
		$where = where_disdel();
		$where[] = ['is_recommend', '=', 1];
		$platformAuthModel = new PlatformAuthModel();
		$authIds = $platformAuthModel->where('team_id', member_team_id(true))->column('platform_id');
		$projectModel = new ProjectModel();
		$where[] = ['platform_id', 'in', $authIds];
		$data = $projectModel->with('currency')->where($where)->field('project_pno,platform_id,project_name,project_cpi,project_id,project_currency')->limit(20)->select();
		$member = MemberService::info(member_id(true), true, true, true);
		$team = TeamService::info(member_team_id(true), true, true, true);
		foreach ($data as $key => $row) {
			$platformAuth = $platformAuthModel->where(['platform_id' => $row['platform_id'], 'team_id' => member_team_id(true)])->find();
			$data[$key]['project_cpi'] = round($data[$key]['project_cpi'] * $data[$key]['currency']['currency_coins'] * (100 - $team['commission_ratio']) / 100 * (100 - $member['rate']) / 100 * (100 - $platformAuth['auth_rate']) / 100, 2);
			unset($data[$key]['currency']);
		}
		return success($data);
	}
	public function markdetaillist()
	{
		$param = $this->params([
			'project_pno/s' => ''
		]);
		$MarkDetailModel = new MarkDetailModel();
		$teamid = member_team_id(true);
		$data = $MarkDetailModel->with(['mark', 'member'])->where('mark_team_id', $teamid)->where('mark_project_pno', $param['project_pno'])->select();
		foreach ($data as $row) {
			$row['member_name'] = $row['member']['nickname'];
			$row->hidden(['member']);
		}
		return success($data);
	}
	public function marklist()
	{
		$MarkModel = new MarkModel();
		$data = $MarkModel->order('sort desc')->select();
		return success($data);
	}
	public function mark()
	{
		$param = $this->params([
			'mark_id/d' => '',
			'project_pno/s' => ''
		]);
		$projectModel = new ProjectModel();
		$project = $projectModel->where('project_pno', $param['project_pno'])->find();
		$MarkDetailModel = new MarkDetailModel();
		$member_id = member_id(true);
		$teamid = member_team_id(true);
		if ($MarkDetailModel->where('mark_user_id', $member_id)->where('mark_project_pno', $param['project_pno'])->find()) {
			return error('You have already tagged this item');
		}
		$MarkDetailModel->save(['mark_user_id' => $member_id, 'mark_project_pno' => $project['project_pno'], 'mark_project_no' => $project['project_no'], 'mark_project_name' => $project['project_name'], 'mark_id' => $param['mark_id'], 'create_time' => date('Y-m-d H:i:s'), 'mark_team_id' => $teamid]);
		return success('mark successfully');
	}
	public function quota()
	{
		domain_verify();
		$param = $this->params([
			'project_pno/s' => ''
		]);
		$projectModel = new ProjectModel();
		$project = $projectModel->where('project_pno', $param['project_pno'])->find();
		$platform = PlatformService::info($project['platform_id']);
		$platformParams = Utils::decodeParam(json_decode(json_encode($platform['params']), true));
		if ($platform['platform_sign'] == 'Innovatemr' && !$project['project_content']) {
			$akey = $platformParams['app_key'];
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL => $platform['platform_quota_url'] . "/" . $project['project_no'],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => [
					"Accept: application/json",
					"x-access-token: $akey"
				],
			]);
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			$content = '';
			if (!$err) {
				$result = json_decode($response, true);
				if ($result['result']) {
					$css = '<style>'
						. '.qs-qual-list{display:flex;flex-direction:column;gap:10px;}'
						. '.qs-qual-item{background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:14px 16px;}'
						. '.qs-qual-q{font-size:13px;font-weight:700;color:rgba(14,255,78,.85);margin-bottom:10px;letter-spacing:.01em;}'
						. '.qs-qual-answers{display:flex;flex-wrap:wrap;gap:6px;}'
						. '.qs-answer-chip{font-size:11px;font-weight:600;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.09);border-radius:20px;padding:3px 10px;color:rgba(255,255,255,0.65);}'
						. '</style>';
					$items = '';
					foreach ($result['result'] as $v) {
						$answers = '';
						foreach ($v['Options'] as $op) {
							foreach ($op as $k => $o) {
								if ($k != 'OptionId') {
									$answers .= '<span class="qs-answer-chip">' . htmlspecialchars($o) . '</span>';
								}
							}
						}
						$items .= '<div class="qs-qual-item">'
							. '<div class="qs-qual-q">' . htmlspecialchars($v['QuestionText']) . '</div>'
							. '<div class="qs-qual-answers">' . $answers . '</div>'
							. '</div>';
					}
					$content = $css . '<div class="qs-qual-list">' . $items . '</div>';
					if ($content) {
						$update['project_content'] = $content;
						$projectModel->where('project_id', $project['project_id'])->update($update);
					}
				}
			}
			return success(['type' => 'content', 'content' => $content]);
		}
		if ($platform['platform_sign'] == 'mirat') {
			$curl = curl_init();

			curl_setopt_array($curl, [
				CURLOPT_URL => str_replace("{surveyNumber}", $project['project_no'], $platform['platform_quota_url']),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode([
					'authorizationKey' => $platformParams['app_key']
				]),
				CURLOPT_HTTPHEADER => [
					"Content-Type: application/json",
					"publisher-authentication-key: " . $platformParams['app_secret']
				],
			]);
			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				$result = json_decode($response, true);
				$rawContent = $this->renderDiv($result['qualification']);
				$css = '<style>'
					. '.qs-mirat-wrap{display:flex;flex-direction:column;gap:2px;}'
					. '.qs-mirat-wrap div{padding:7px 14px;border-bottom:1px solid rgba(255,255,255,0.04);font-size:13px;color:inherit;line-height:1.6;}'
					. '.qs-mirat-wrap strong{color:rgba(255,255,255,0.55);font-weight:600;margin-right:6px;}'
					. '</style>';
				$content = $css . '<div class="qs-mirat-wrap">' . $rawContent . '</div>';
				if ($content) {
					$update['project_content'] = $content;
					$projectModel->where('project_id', $project['project_id'])->update($update);
				}
				return success(['type' => 'content', 'content' => $content]);
			}
		}
		if ($platform['platform_sign'] == 'Gowebsurveys') {
			$akey = $platformParams['app_key'];
			$aid = $platformParams['app_id'];

			// 1. Fetch Quota Status
			$curlQuota = curl_init();
			curl_setopt_array($curlQuota, [
				CURLOPT_URL => $platform['platform_quota_url'],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode(["surveyIDs" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no']]),
				CURLOPT_HTTPHEADER => [
					"Accept: application/json",
					"Authorization: $akey",
					"payload: $aid",
					"Content-Type: application/json"
				],
			]);
			$resQuota = curl_exec($curlQuota);
			$errQuota = curl_error($curlQuota);
			curl_close($curlQuota);

			if ($errQuota) {
				return success(['type' => 'content', 'content' => 'Failed to fetch quota data']);
			}

			$quotaData = json_decode($resQuota, true);

			// 2. Fetch Qualification
			$baseUrl = str_replace('/quotaStatus', '', $platform['platform_quota_url']);
			$curlQual = curl_init();
			curl_setopt_array($curlQual, [
				CURLOPT_URL => $baseUrl . '/getQualification',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode(["surveyID" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no']]),
				CURLOPT_HTTPHEADER => [
					"Accept: application/json",
					"Authorization: $akey",
					"payload: $aid",
					"Content-Type: application/json"
				],
			]);
			$resQual = curl_exec($curlQual);
			$errQual = curl_error($curlQual);
			curl_close($curlQual);

			$qualData = json_decode($resQual, true);

			$logData = "================ GOWEBSURVEYS QUOTA LOG ================\n";
			$logData .= "Time: " . date('Y-m-d H:i:s') . "\n";
			$logData .= "Project: " . ($project['project_pno'] ?? 'N/A') . " (ID: " . ($project['project_no'] ?? 'N/A') . ")\n";
			$logData .= "--- Step 1: Quota Status ---\n";
			$logData .= "URL: " . $platform['platform_quota_url'] . "\n";
			$logData .= "Headers: Authorization: $akey | payload: $aid\n";
			$logData .= "Payload: " . json_encode(["surveyIDs" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no']]) . "\n";
			$logData .= "Response: " . $resQuota . "\n";
			$logData .= "Error: " . $errQuota . "\n";
			$logData .= "--- Step 2: Qualification ---\n";
			$logData .= "URL: " . $baseUrl . '/getQualification' . "\n";
			$logData .= "Payload: " . json_encode(["surveyID" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no']]) . "\n";
			$logData .= "Response: " . $resQual . "\n";
			$logData .= "Error: " . $errQual . "\n";
			$logData .= "=======================================================\n\n";
			file_put_contents(public_path() . 'api_debug.log', $logData, FILE_APPEND);

			if ($quotaData && isset($quotaData['apiStatus']) && $quotaData['apiStatus'] == 1) {
				return success([
					'type' => 'structured',
					'surveyInfo' => $quotaData['surveyInfo'] ?? [],
					'qualData' => (!$errQual && $qualData) ? $qualData : [],
					'project' => [
						'project_quota' => $project['project_quota'],
						'project_complete' => $project['project_complete']
					]
				]);
			} else {
				return success([
					'type' => 'content',
					'content' => $quotaData['apiMessages'] ?? 'Unable to fetch quota details.'
				]);
			}
		}
		if ($platform['platform_sign'] == 'Pollsopinion') {
			$curl = curl_init();
			$akey = $platformParams['app_key'];
			$aid = $platformParams['app_id'];
			curl_setopt_array($curl, [
				CURLOPT_URL => $platform['platform_quota_url'],
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode([
					"surveyIDs" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no']
				]),
				CURLOPT_HTTPHEADER => [
					"Accept: application/json",
					"Authorization: $akey",
					"payload: $aid"
				],
			]);
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			$logData = "================ POLLSOPINION QUOTA LOG ================\n";
			$logData .= "Time: " . date('Y-m-d H:i:s') . "\n";
			$logData .= "Project: " . ($project['project_pno'] ?? 'N/A') . " (ID: " . ($project['project_no'] ?? 'N/A') . ")\n";
			$logData .= "URL: " . $platform['platform_quota_url'] . "\n";
			$logData .= "Headers: Authorization: $akey | payload: $aid\n";
			$logData .= "Payload: " . json_encode(["surveyIDs" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no']]) . "\n";
			$logData .= "Response: " . $response . "\n";
			$logData .= "Error: " . $err . "\n";
			$logData .= "========================================================\n\n";
			file_put_contents(public_path() . 'api_debug.log', $logData, FILE_APPEND);

			if ($err) {
				return success(['type' => 'content', 'content' => 'get error']);
			} else {
				$result = json_decode($response, true);
				if ($result['apiStatus'] == 1) {
					$css = '<style>'
						. '.quota_table{width:100%;border-collapse:collapse;font-size:13px;}'
						. '.quota_table th{text-align:left;padding:10px 14px;color:#888;font-weight:500;border-bottom:1px solid rgba(255,255,255,0.06);background:rgba(255,255,255,0.04);white-space:nowrap;}'
						. '.quota_table td{padding:10px 14px;border-bottom:1px solid rgba(255,255,255,0.04);color:inherit;vertical-align:top;}'
						. '.quota_table tr:last-child td{border-bottom:none;}'
						. '.quota_table tr:nth-child(even) td{background:rgba(255,255,255,0.015);}'
						. '.qs-panel{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px;}'
						. '.qs-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:14px 16px;}'
						. '.qs-label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.06em;font-weight:500;margin-bottom:6px;}'
						. '.qs-value{font-size:22px;font-weight:700;color:inherit;}'
						. '.qs-value.ok{color:rgba(14,255,78,.85);}'
						. '.qs-panel-wrap{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:10px;overflow:hidden;margin-bottom:16px;}'
						. '</style>';
					$remain = $result['surveyInfo'][0]['surveyTargetCount'] ?? 0;
					$content = $css
						. '<div class="qs-panel">'
						. '<div class="qs-card"><div class="qs-label">Total Quota</div><div class="qs-value">' . $project['project_quota'] . '</div></div>'
						. '<div class="qs-card"><div class="qs-label">Remaining</div><div class="qs-value ok">' . $remain . '</div></div>'
						. '<div class="qs-card"><div class="qs-label">Completed</div><div class="qs-value">' . $project['project_complete'] . '</div></div>'
						. '</div>';
					foreach ($result['surveyInfo'] as $v) {
						$content .= '<table class="quota_table"><thead><tr><th>quota</th><th>Remaining quota</th><th>Quota Detail</th></tr></thead><tbody>';
						foreach ($v['Quota'] as $op) {
							$content .= '<tr>';

							if ($op['conditions'][0]['profileQuestionKey'] == 'age') {
								$content .= '<td>Age</td><td>' . $op['conditions'][0]['min'] . '–' . $op['conditions'][0]['max'] . '</td>';
								$remaining = $op['remainingQuota'][0] ?? 0;
								$colorClass = $remaining > 0 ? 'color:rgba(14,255,78,.85)' : 'color:#f87171';
								$content .= '<td style="font-weight:600;' . $colorClass . '">' . $remaining . '</td>';
							} else {
								$remaining = $op['remainingQuota'][0] ?? 0;
								$colorClass = $remaining > 0 ? 'color:rgba(14,255,78,.85)' : 'color:#f87171';
								$content .= '<td>' . htmlspecialchars($op['conditions'][0]['profileQuestionKey']) . ': ' . htmlspecialchars($op['conditions'][0]['OptionText'] ?? '') . '</td>';
								$content .= '<td style="font-weight:600;' . $colorClass . '">' . $remaining . '</td>';
							}
							$content .= '<td>' . htmlspecialchars($v['projectBrief'] ?? '') . '</td></tr>';
						}
						$content .= '</tbody></table>';

					}
					if ($content) {
						$update['project_content'] = '<ul>' . $content . '</ul>';
						$projectModel->where('project_id', $project['project_id'])->update($update);
					}
					return success(['type' => 'content', 'content' => $content, 'surveyInfo' => $result]);
				} else {
					return success(['type' => 'content', 'content' => $result['apiMessages']]);
				}

			}
		}
		if ($platform['platform_sign'] == 'Zamplia') {
			$akey = $platformParams['app_key'];
			$cleanUrl = str_replace(' ', '', $platform['platform_quota_url']);
			$url = rtrim($cleanUrl, '/') . '?SurveyId=' . $project['project_no'];

			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => [
					"Accept: application/json",
					"ZAMP-KEY: $akey"
				],
			]);
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			
			$qualUrl = str_replace('GetSurveyQuotas', 'GetSurveyQualifications', rtrim($cleanUrl, '/')) . '?SurveyId=' . $project['project_no'];
			$curlQual = curl_init();
			curl_setopt_array($curlQual, [
				CURLOPT_URL => $qualUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => [
					"Accept: application/json",
					"ZAMP-KEY: $akey"
				],
			]);
			$resQual = curl_exec($curlQual);
			$errQual = curl_error($curlQual);
			curl_close($curlQual);
			
			$demoUrl = str_replace(['GetSurveyQuotas', 'GetSurveyQualifications'], 'GetDemoGraphics', rtrim($cleanUrl, '/')) . '?LanguageId=' . ($project['project_code'] ? $project['project_code'] : 9);
			$curlDemo = curl_init();
			curl_setopt_array($curlDemo, [
				CURLOPT_URL => $demoUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => [
					"Accept: application/json",
					"ZAMP-KEY: $akey"
				],
			]);
			$resDemo = curl_exec($curlDemo);
			$errDemo = curl_error($curlDemo);
			curl_close($curlDemo);
			
			// --- DEBUG LOGGING ---
			$logData = "================ ZAMPLIA QUOTA LOG ================\n";
			$logData .= "Time: " . date('Y-m-d H:i:s') . "\n";
			$logData .= "Project PNO: " . $project['project_pno'] . "\n";
			$logData .= "URL [Quota]: " . $url . "\n";
			$logData .= "Response [Quota]: " . $response . "\n";
			$logData .= "URL [Qual]: " . $qualUrl . "\n";
			$logData .= "Response [Qual]: " . $resQual . "\n";
			$logData .= "URL [Demo]: " . $demoUrl . "\n";
			$logData .= "Response [Demo]: " . $resDemo . "\n";
			$logData .= "====================================================\n\n";
			file_put_contents(__DIR__.'/zamplia_debug.txt', $logData, FILE_APPEND);
			
			if ($err) {
				return success(['type' => 'content', 'content' => 'get error']);
			} else {
				$result = json_decode($response, true);
				$qualResult = (!$errQual && $resQual) ? json_decode($resQual, true) : [];
				
				$demoResult = (!$errDemo && $resDemo) ? json_decode($resDemo, true) : [];
				$demoQuestions = [];
				$demoAnswers = [];
				if (isset($demoResult['success']) && $demoResult['success'] && !empty($demoResult['result']['data'])) {
					foreach ($demoResult['result']['data'] as $d) {
						$qId = $d['QuestionID'];
						$demoQuestions[$qId] = $d['QuestionText'] ?? $qId;
						if (isset($d['AnswerCodes']) && is_array($d['AnswerCodes'])) {
							foreach ($d['AnswerCodes'] as $ans) {
								$ac = $ans['AnswerCode'] ?? '';
								$at = $ans['AnswerText'] ?? $ac;
								$demoAnswers[$qId . '_' . $ac] = $at;
							}
						}
					}
				}
				
				if (isset($result['success']) && $result['success']) {
					$css = '<style>'
						. '.quota_table{width:100%;border-collapse:collapse;font-size:13px;}'
						. '.quota_table th{text-align:left;padding:10px 14px;color:#888;font-weight:500;border-bottom:1px solid rgba(255,255,255,0.06);background:rgba(255,255,255,0.04);white-space:nowrap;}'
						. '.quota_table td{padding:10px 14px;border-bottom:1px solid rgba(255,255,255,0.04);color:inherit;vertical-align:top;}'
						. '.quota_table tr:last-child td{border-bottom:none;}'
						. '.quota_table tr:nth-child(even) td{background:rgba(255,255,255,0.015);}'
						. '.qs-panel{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px;}'
						. '.qs-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:14px 16px;}'
						. '.qs-label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.06em;font-weight:500;margin-bottom:6px;}'
						. '.qs-value{font-size:22px;font-weight:700;color:inherit;}'
						. '.qs-value.ok{color:rgba(14,255,78,.85);}'
						. '.qs-section-title{font-size:12px;font-weight:600;color:#a9a9ca;text-transform:uppercase;letter-spacing:.05em;margin:18px 0 8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,0.06);}'
						. '.qs-panel-wrap{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:10px;overflow:hidden;margin-bottom:16px;}'
						. '</style>';
					$content = $css;
					
					$surveyData = $result['result']['data'][0] ?? [];
					$totalQuota = $surveyData['TotalQuotaCount'] ?? 0;
					
					$content .= '<div class="qs-panel">'
						. '<div class="qs-card"><div class="qs-label">Total Quota</div><div class="qs-value">' . $totalQuota . '</div></div>'
						. '<div class="qs-card"><div class="qs-label">Completed</div><div class="qs-value">' . $project['project_complete'] . '</div></div>'
						. '<div class="qs-card"><div class="qs-label">Remaining</div><div class="qs-value ok">' . max(0, $totalQuota - $project['project_complete']) . '</div></div>'
						. '</div>';
					
					if (!empty($surveyData['QuotaQualifications'])) {
						$content .= '<div class="qs-section-title">Quota Breakdown</div><div class="qs-panel-wrap"><table class="quota_table"><thead><tr><th>Condition</th><th>Total Qualification Count</th><th>Total Quota Count</th></tr></thead><tbody>';
						foreach ($surveyData['QuotaQualifications'] as $q) {
							$answerText = $q['AnswerText'] ?? '';
							$totalQualCount = $q['TotalQualificationCount'] ?? 0;
							$qTotalQuotaCount = $q['TotalQuotaCount'] ?? 0;
							
							$content .= '<tr>';
							$content .= '<td><p style="padding-left:20px;font-size:14px;margin-top:10px;">' . $answerText . '</p></td>';
							$content .= '<td><p style="padding-left:20px;font-size:14px;margin-top:10px;">' . $totalQualCount . '</p></td>';
							$content .= '<td><p style="padding-left:20px;font-size:14px;margin-top:10px;">' . $qTotalQuotaCount . '</p></td>';
							$content .= '</tr>';
						}
						$content .= '</tbody></table></div>';
					}

					if (isset($qualResult['success']) && $qualResult['success'] && !empty($qualResult['result']['data'])) {
						$content .= '<div class="qs-section-title">Qualifications</div>';
						$content .= '<div class="qs-panel-wrap"><table class="quota_table"><thead><tr><th>Question</th><th>Type</th><th>Answers</th></tr></thead><tbody>';
						foreach ($qualResult['result']['data'] as $q) {
							$qId = $q['QuestionID'] ?? '';
							$qText = $demoQuestions[$qId] ?? $qId;
							$qType = $q['QuestionType'] ?? '';
							
							$mappedAnswers = [];
							if (isset($q['AnswerCodes'])) {
								foreach ($q['AnswerCodes'] as $ac) {
									$mappedAnswers[] = $demoAnswers[$qId . '_' . $ac] ?? $ac;
								}
							}
							$answers = implode(', ', $mappedAnswers);
							
							$content .= '<tr>';
							$content .= '<td><p style="padding-left:20px;font-size:14px;margin-top:10px;">' . $qText . '</p></td>';
							$content .= '<td><p style="padding-left:20px;font-size:14px;margin-top:10px;">' . $qType . '</p></td>';
							$content .= '<td><p style="padding-left:20px;font-size:14px;margin-top:10px;">' . $answers . '</p></td>';
							$content .= '</tr>';
						}
						$content .= '</tbody></table></div>';
					}

					if ($content) {
						$update['project_content'] = '<ul>' . $content . '</ul>';
						$update['project_quota'] = $totalQuota;
						$projectModel->where('project_id', $project['project_id'])->update($update);
					}
					return success(['type' => 'content', 'content' => $content, 'surveyInfo' => $result, 'qualData' => $qualResult]);

				} else {
					return success(['type' => 'content', 'content' => $result['message'] ?? 'Failed']);
				}
			}
		}
		if (filter_var($project['project_content'], FILTER_VALIDATE_URL) !== false) {
			return success(['type' => 'link', 'content' => $project['project_content']]);
		}
		return success(['type' => 'content', 'content' => $project['project_content']]);
	}
	private function renderDiv($data, $indent = 0)
	{
		$html = "";
		$space = str_repeat("&nbsp;", $indent);

		if (is_array($data)) {
			foreach ($data as $key => $value) {
				// 数组数字key，不显示key
				$keyLabel = is_numeric($key) ? "" : "<strong>$key:</strong> ";

				if (is_array($value)) {
					// 有子结构
					$html .= "$space<div>$keyLabel</div>";
					$html .= $this->renderDiv($value, $indent + 1);
				} else {
					// 普通值
					$html .= "$space<div>$keyLabel$value</div>";
				}
			}
		}

		return $html;
	}
	public function wall_copy()
	{
		domain_verify();
		$param = $this->params([
			'platform_id/d' => ''
		]);
		$platformModel = new PlatformModel();
		$platform = $platformModel->where('platform_id', $param['platform_id'])->where(where_disdel())->find();
		$api_token = api_token();
		$protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https" : "http";
		$url = rawurldecode("$protocol://$_SERVER[HTTP_HOST]");
		if ($platform) {
			if ($platform['platform_persona_template'] > 0) {
				return success($url . '/link.html?platform_id=' . $platform['platform_id'] . '&key=' . $api_token);
			}
			return success($url . '/api/member.Platform/wall_link?pid=' . $platform['platform_id'] . '&key=' . $api_token);
		} else {
			return error('The platform does not exist');
		}
	}
	public function copy()
	{
		domain_verify();
		$param = $this->params([
			'project_pno/s' => ''
		]);
		$member_id = member_id(true);
		$projectModel = new ProjectModel();
		$project = $projectModel->with('platform')->where('project_pno', $param['project_pno'])->where(where_disdel())->find();
		$protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https" : "http";
		$url = rawurldecode("$protocol://$_SERVER[HTTP_HOST]");
		$api_token = api_token();
		if ($project) {
			if ($project['project_persona_template'] > 0) {
				$personaModel = new PersonaModel();
				$persona = $personaModel->where('persona_id', $project['project_persona_template'])->find();
				if ($persona) {
					if ($persona['persona_type'] == 0) {
						return success($url . '/link.html?pid=' . $project['project_pno'] . '&key=' . $api_token);
					} else {
						return success($url . '/api/member.Platform/link?pid=' . $project['project_pno'] . '&key=' . $api_token);
					}
				} else {
					return error('The persona template does not exist');
				}
			} else if ($project['platform']['platform_persona_template'] > 0) {
				$personaModel = new PersonaModel();
				$persona = $personaModel->where('persona_id', $project['platform']['platform_persona_template'])->find();
				if ($persona) {
					if ($persona['persona_type'] == 0) {
						return success($url . '/link.html?pid=' . $project['project_pno'] . '&key=' . $api_token);
					} else {
						return success($url . '/api/member.Platform/link?pid=' . $project['project_pno'] . '&key=' . $api_token);
					}
				} else {
					return error('The persona template does not exist');
				}
			}
			return success($url . '/api/member.Platform/link?pid=' . $project['project_pno'] . '&key=' . $api_token);
		} else {
			return error('The project does not exist');
		}
	}
	public function wall_link()
	{
		$param = $this->params([
			'pid/d' => '',
			'key/s' => '',
			'anser/a' => []
		]);
		$decode = TokenService::decode($param['key']);
		$member_id = $decode->data->member_id;
		$platformModel = new PlatformModel();
		$platformAuthModel = new PlatformAuthModel();
		$platform = $platformModel->where('platform_id', $param['pid'])->find();
		if (empty($platform['params'])) {
			return 'The platform has not configured docking parameters';
		}
		$authIds = $platformAuthModel->where(['team_id' => $decode->data->team_id])->column('platform_id');
		if (!in_array($platform['platform_id'], $authIds)) {
			return error('未获得该平台授权');
		}
		$platformParams = Utils::decodeParam(json_decode($platform['params'], true));
		if ($platform) {
			if ($platform['platform_persona_template'] > 0) {
				$PersonaModel = new PersonaModel();
				$persona = $PersonaModel->where('persona_id', $platform['platform_persona_template'])->find();
				if ($persona['persona_type'] == 0) {
					if (empty($param['anser'])) {
						return 'No personnel information filled in';
					}
					$PersonaDataModel = new PersonaDataModel();
					$personaData = $PersonaDataModel->where('persona_id', $platform['platform_persona_template'])->select()->toArray();

					foreach ($personaData as $row) {
						if (isset($param['anser'][$row['persona_data_id']])) {
							if ($row['persona_data_must'] == 1 && !$param['anser'][$row['persona_data_id']]) {
								return $row['persona_data_holder'];
							}
							if (($row['persona_data_type'] == 'radio' || $row['persona_data_type'] == 'select') && $param['anser'][$row['persona_data_id']]) {
								$values = json_decode($row['persona_data_values'], true);
								foreach ($values as $v) {
									$check[] = $v['value'];
								}
								if (!in_array($param['anser'][$row['persona_data_id']], $check)) {
									return $row['persona_data_name'] . ' error';
								}
							}
							if ($row['persona_data_type'] == 'input' && $param['anser'][$row['persona_data_id']]) {
								if (strlen($param['anser'][$row['persona_data_id']]) > 100) {
									return 'Exceeding the word limit';
								}
								$pattern = '/[!@#$%^&*()_=+{}\[\]|;:"<>,?\/\\`~]/';
								if (preg_match($pattern, $param['anser'][$row['persona_data_id']])) {
									return 'Prohibit the input of special characters';
								}
							}
							if ($row['persona_data_type'] == 'date' && $param['anser'][$row['persona_data_id']]) {
								if (!preg_match('/^\d{10,13}$/', $param['anser'][$row['persona_data_id']])) {
									return 'Date is illegal';
								}
								$param['anser'][$row['persona_data_id']] = date('Y-m-d', $param['anser'][$row['persona_data_id']]);
							}
							$anser[] = ['name' => $row['persona_data_name'], 'value' => $param['anser'][$row['persona_data_id']]];
						} else {
							return 'illegal request';
						}
					}
				}
			}
			$time = time();
			$uuid = md5($time . bin2hex(random_bytes(16)));
			$flowingModel = new FlowingModel();
			$ipInfo = Utils::ipInfo();
			$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
			$ret = $flowingModel->save(['uuid' => $uuid, 'member_id' => $member_id, 'ip' => $ipInfo['ip'], 'rs_content' => empty($param['anser']) ? NULL : json_encode($anser), 'create_time' => date('Y-m-d H:i:s'), 'ua' => $userAgent]);
			if ($ret) {
				switch ($platform['platform_sign']) {
					case 'Notik':
						$url = 'https://notik.me/coins?api_key=' . $platformParams['app_key'] . '&pub_id=' . $platformParams['pub_id'] . '&app_id=' . $platformParams['app_id'] . '&user_id=' . $uuid;
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					case 'Surveyxa':
						$url = 'https://surveyxa.com/offerwall?id=' . $platformParams['app_key'] . '&uid=' . $uuid;
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					case 'Opinionuniverse':
						$url = 'https://opinionuniverse.com/offerwall.php?pubid=' . $platformParams['pub_id'] . '&sid=' . $uuid . '&app_id=' . $platformParams['app_id'] . '&apikey=' . $platformParams['app_key'];
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					case 'Upwall':
						$url = 'https://offerwall.upwall.net/?app_id=' . $platformParams['app_id'] . '&userid=' . $uuid;
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					case 'Wannads':
						$url = 'https://earn.wannads.com/surveywall?apiKey=' . $platformParams['app_key'] . '&userId=' . $uuid;
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					case 'Meeduo':
						$url = 'https://www.meeduo.com/wall.mdq?sid=' . $platformParams['app_id'] . '&memberid=' . $uuid;
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					case 'Pollfish':
						$url = 'https://wss.pollfish.com/v2/device/register/true?json=%7B%22api_key%22%3A%22' . $platformParams['app_key'] . '%22%2C%22offerwall%22%3A%22true%22%2C%22debug%22%3A%22false%22%2C%22ip%22%3A%22' . $ipInfo['ip'] . '%22%2C%22device_id%22%3A%22' . $Txid . '%22%2C%22timestamp%22%3A%22' . time() . '000%22%2C%22encryption%22%3A%22NONE%22%2C%22version%22%3A%229%22%2C%22device_descr%22%3A%22UNKNOWN%22%2C%22os%22%3A%223%22%2C%22os_ver%22%3A%2210.13.2%22%2C%22scr_h%22%3A%221178%22%2C%22src_w%22%3A%221920%22%2C%22scr_size%22%3A%2223.46429949294128%22%2C%22manufacturer%22%3A%22UNKNOWN%22%2C%22locale%22%3A%22en-US%2Cen%2Cel%22%2C%22request_uuid%22%3A%22' . $uuid . '%22%2C%22hardware_accelerated%22%3A%22false%22%2C%22video%22%3A%22true%22%2C%22survey_format%22%3A%220%22%7D&dontencrypt=true&webplugin=false&iframewidth=400px&position=BOTTOM_RIGHT';
						echo "<script>window.location.href='" . $url . "';</script>";
						break;
					case 'Bitlabs':
						$url = 'https://web.bitlabs.ai/?uid=' . $uuid . '&token=' . $platformParams['app_token'];
						echo "<script>window.location.href='" . $url . "';</script>";
						break;
					case 'Enline':
						$url = 'https://enlignesurvey.com/offerwall.php?pubid=' . $platformParams['pub_id'] . '&sid=' . $uuid . '&sid2=' . md5($uuid . $platformParams['app_secret']);
						echo "<script>window.location.href='" . $url . "';</script>";
						break;
					case 'Rapidoreach':
						$checksum = md5($uuid . "-" . $platformParams['app_id'] . "-" . $platformParams['app_key']);
						$url = 'https://www.rapidoreach.com/ofw/?userId=' . $uuid . '-' . $platformParams['app_id'] . '-' . $checksum;
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					case 'Lootably':
						$url = 'https://wall.lootably.com/?placementID=' . $platformParams['app_id'] . '&sid=' . $uuid;
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					case 'Theoremreach':
						$url = 'https://theoremreach.com/respondent_entry/direct?api_key=' . $platformParams['app_key'] . '&user_id=' . $uuid . '&transaction_id=' . md5($uuid);
						echo '<script>window.location.href="' . $url . '";</script>';
						break;
					default:
						echo 'This project is not configured with links';
						break;
				}
			} else {
				echo 'uuid generation failed, please try again';
			}
		} else {
			echo 'platform does not exist or is not open';
		}
	}
	private function getIpSecurityInfo(string $ip, string $apiKey): ?array
	{
		$url = "https://api.ipbot.com/{$ip}";

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"X-API-Key: {$apiKey}"
		]);

		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			curl_close($ch);
			return null; // 请求失败
		}
		curl_close($ch);

		$data = json_decode($response, true);
		if (!$data || !isset($data['security'])) {
			return null; // JSON 解析失败或字段不存在
		}
		// 返回和 jq 相同的结构
		return [
			'risk_score' => $data['security']['risk_score'] ?? null,
			'threat_level' => $data['security']['threat_level'] ?? null,
			'risk_reasons' => $data['security']['risk_reasons'] ?? [],
			'threat_lists' => $data['security']['threat_lists'] ?? []
		];
	}
	public function link()
	{
		$param = $this->params([
			'pid/s' => '',
			'key/s' => '',
			'anser/a' => []
		]);
		$decode = TokenService::decode($param['key']);
		$member_id = $decode->data->member_id;
		$projectModel = new ProjectModel();
		$project = $projectModel->where('project_pno', $param['pid'])->where(where_disdel())->find();
		$platformModel = new PlatformModel();
		$platform = $platformModel->where('platform_id', $project['platform_id'])->where(where_disdel())->find();
		if ($project && $platform) {
			$platformAuthModel = new PlatformAuthModel();
			$authIds = $platformAuthModel->where(['team_id' => $decode->data->team_id])->column('platform_id');
			if (!in_array($platform['platform_id'], $authIds)) {
				return error('未获得该平台授权');
			}
			$time = time();
			$uuid = md5($time . bin2hex(random_bytes(16)));
			$flowingModel = new FlowingModel();
			$ipInfo = Utils::ipInfo();
			$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
			$project->project_click += 1;
			$project->save();
			if ($project['project_persona_template'] > 0) {
				$PersonaModel = new PersonaModel();
				$persona = $PersonaModel->where('persona_id', $project['project_persona_template'])->find();
				if ($persona['persona_type'] == 0) {
					if (empty($param['anser'])) {
						return 'No personnel information filled in';
					}
					$PersonaDataModel = new PersonaDataModel();
					$personaData = $PersonaDataModel->where('persona_id', $project['project_persona_template'])->select()->toArray();

					foreach ($personaData as $row) {
						if (isset($param['anser'][$row['persona_data_id']])) {
							if ($row['persona_data_must'] == 1 && !$param['anser'][$row['persona_data_id']]) {
								return $row['persona_data_holder'];
							}
							if (($row['persona_data_type'] == 'radio' || $row['persona_data_type'] == 'select') && $param['anser'][$row['persona_data_id']]) {
								$values = json_decode($row['persona_data_values'], true);
								foreach ($values as $v) {
									$check[] = $v['value'];
								}
								if (!in_array($param['anser'][$row['persona_data_id']], $check)) {
									return $row['persona_data_name'] . ' error';
								}
							}
							if ($row['persona_data_type'] == 'input' && $param['anser'][$row['persona_data_id']]) {
								if (strlen($param['anser'][$row['persona_data_id']]) > 100) {
									return 'Exceeding the word limit';
								}
								$pattern = '/[!@#$%^&*()_=+{}\[\]|;:"<>,?\/\\`~]/';
								if (preg_match($pattern, $param['anser'][$row['persona_data_id']])) {
									return 'Prohibit the input of special characters';
								}
							}
							if ($row['persona_data_type'] == 'date' && $param['anser'][$row['persona_data_id']]) {
								if (!preg_match('/^\d{10,13}$/', $param['anser'][$row['persona_data_id']])) {
									return 'Date is illegal';
								}
								$param['anser'][$row['persona_data_id']] = date('Y-m-d', $param['anser'][$row['persona_data_id']]);
							}
							$anser[] = ['name' => $row['persona_data_name'], 'value' => $param['anser'][$row['persona_data_id']]];
						} else {
							return 'illegal request';
						}
					}
				}
			} else if ($platform['platform_persona_template'] > 0) {
				$PersonaModel = new PersonaModel();
				$persona = $PersonaModel->where('persona_id', $platform['platform_persona_template'])->find();
				if ($persona['persona_type'] == 0) {
					if (empty($param['anser'])) {
						return 'No personnel information filled in';
					}
					$PersonaDataModel = new PersonaDataModel();
					$personaData = $PersonaDataModel->where('persona_id', $platform['platform_persona_template'])->select()->toArray();

					foreach ($personaData as $row) {
						if (isset($param['anser'][$row['persona_data_id']])) {
							if ($row['persona_data_must'] == 1 && !$param['anser'][$row['persona_data_id']]) {
								return $row['persona_data_holder'];
							}
							if (($row['persona_data_type'] == 'radio' || $row['persona_data_type'] == 'select') && $param['anser'][$row['persona_data_id']]) {
								$values = json_decode($row['persona_data_values'], true);
								foreach ($values as $v) {
									$check[] = $v['value'];
								}
								if (!in_array($param['anser'][$row['persona_data_id']], $check)) {
									return $row['persona_data_name'] . ' error';
								}
							}
							if ($row['persona_data_type'] == 'input' && $param['anser'][$row['persona_data_id']]) {
								if (strlen($param['anser'][$row['persona_data_id']]) > 100) {
									return 'Exceeding the word limit';
								}
								$pattern = '/[!@#$%^&*()_=+{}\[\]|;:"<>,?\/\\`~]/';
								if (preg_match($pattern, $param['anser'][$row['persona_data_id']])) {
									return 'Prohibit the input of special characters';
								}
							}
							if ($row['persona_data_type'] == 'date' && $param['anser'][$row['persona_data_id']]) {
								if (!preg_match('/^\d{10,13}$/', $param['anser'][$row['persona_data_id']])) {
									return 'Date is illegal';
								}
								$param['anser'][$row['persona_data_id']] = date('Y-m-d', $param['anser'][$row['persona_data_id']]);
							}
							$anser[] = ['name' => $row['persona_data_name'], 'value' => $param['anser'][$row['persona_data_id']]];
						} else {
							return 'illegal request';
						}
					}
				}
			}
			$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
			$ret = $flowingModel->save(['uuid' => $uuid, 'member_id' => $member_id, 'ip' => $ipInfo['ip'], 'rs_content' => empty($param['anser']) ? NULL : json_encode($anser), 'create_time' => date('Y-m-d H:i:s'), 'project_id' => $project['project_id'], 'ua' => $userAgent]);
			if ($ret) {
				switch ($platform['platform_sign']) {
					case 'mirat':
						$platformParams = Utils::decodeParam(json_decode($platform['params'], true));
						$curl = curl_init();
						curl_setopt_array($curl, [
							CURLOPT_URL => str_replace("{surveyNumber}", $project['project_no'], $platform['platform_click_url']),
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => "",
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 30,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => "POST",
							CURLOPT_POSTFIELDS => json_encode([
								'authorizationKey' => $platformParams['app_key']
							]),
							CURLOPT_HTTPHEADER => [
								"Content-Type: application/json",
								"publisher-authentication-key: " . $platformParams['app_secret']
							],
						]);

						$response = curl_exec($curl);
						$err = curl_error($curl);
						curl_close($curl);
						if ($err) {
							echo "cURL Error #:" . $err;
						} else {
							$result = json_decode($response, true);
							echo '<script>window.location.href="' . str_replace("xxxxx", $uuid, $result['liveLink']) . '";</script>';
						}
						break;
					case 'Zamplia':
						if ($project['is_api'] == 1) {
							$platformParams = Utils::decodeParam(json_decode($platform['params'], true));
							$curl = curl_init();
							$akey = $platformParams['app_key'];
							$protocol = 'http';
							if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
								$protocol = 'https';
							}
							$param = http_build_query([
								'SurveyId' => $project['project_no'],
								'IpAddress' => $ipInfo['ip'],
								'TransactionId' => $uuid,
								'uid' => $uuid
							]);
							$domainName = $_SERVER['HTTP_HOST'];
							$url = $protocol . '://' . $domainName;
							curl_setopt_array($curl, [
								CURLOPT_URL => $platform['platform_click_url'] . '?' . $param,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'GET',
								CURLOPT_HTTPHEADER => [
									"Accept: application/json",
									"ZAMP-KEY: $akey"
								],
							]);
							$response = curl_exec($curl);
							$err = curl_error($curl);
							curl_close($curl);
							if ($err) {
								echo "Jump failed, please try again";
							} else {
								$result = json_decode($response, true);
								echo '<script>window.location.href="' . $result['result']['data'][0]['LiveLink'] . '";</script>';
							}
						} else {
							echo '<script>window.location.href="' . $project['project_click_url'] . $uuid . '";</script>';
						}
						break;
					case 'Notik':
						echo '<script>window.location.href="' . str_replace("[user_id]", $uuid, $project['project_click_url']) . '";</script>';
						break;
					case 'Innovatemr':
						echo '<script>window.location.href="' . $project['project_click_url'] . $uuid . '";</script>';
						break;
					case 'MarketXcel':
						echo '<script>window.location.href="' . str_replace("[identifier]", $uuid, $project['project_click_url']) . '";</script>';
						break;
					case 'Gowebsurveys':

						$platformParams = Utils::decodeParam(json_decode($platform['params'], true));
						$curl = curl_init();
						$akey = $platformParams['app_key'];
						$aid = $platformParams['app_id'];
						$protocol = 'http';
						if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
							$protocol = 'https';
						}
						$domainName = $_SERVER['HTTP_HOST'];
						$url = $protocol . '://' . $domainName;
						$postData = json_encode([
							"surveyID" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no'],
							"SuccessLink" => $url . '/api/index/callback?platform=Gowebsurveys&uid={{panellist_id}}&status=C',
							"disQualifiedLink" => $url . '/api/index/callback?platform=Gowebsurveys&uid={{panellist_id}}&status=S',
							"TermLink" => $url . '/api/index/callback?platform=Gowebsurveys&uid={{panellist_id}}&status=T',
							"OverQuotaLink" => $url . '/api/index/callback?platform=Gowebsurveys&uid={{panellist_id}}&status=Q',
							"useStaticLink" => 0
						]);
						curl_setopt_array($curl, [
							CURLOPT_URL => $platform['platform_click_url'],
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS => $postData,
							CURLOPT_HTTPHEADER => [
								"Accept: application/json",
								"Authorization: $akey",
								"payload: $aid",
								"Content-Type: application/json"
							],
						]);
						$response = curl_exec($curl);
						$err = curl_error($curl);
						curl_close($curl);
                        
						$logData = "================ GOWEBSURVEYS LINK LOG ================\n";
						$logData .= "Time: " . date('Y-m-d H:i:s') . "\n";
						$logData .= "Project: " . ($project['project_pno'] ?? 'N/A') . " (ID: " . ($project['project_no'] ?? 'N/A') . ")\n";
						$logData .= "URL: " . $platform['platform_click_url'] . "\n";
						$logData .= "Headers: Authorization: $akey | payload: $aid\n";
						$logData .= "Payload: " . $postData . "\n";
						$logData .= "Response: " . $response . "\n";
						$logData .= "Error: " . $err . "\n";
						$logData .= "=======================================================\n\n";
						file_put_contents(public_path() . 'api_debug.log', $logData, FILE_APPEND);

						if ($err) {
							echo "Jump failed, please try again";
						} else {
							$result = json_decode($response, true);
							if ($result['apiStatus'] == 1) {
								$pos = strpos($result['surveyInfo'][0]['SurveyEntryUrl'], '&pid=');
								$project_click_url = substr($result['surveyInfo'][0]['SurveyEntryUrl'], 0, $pos) . '&pid=' . $uuid;
								echo '<script>window.location.href="' . $project_click_url . '";</script>';
							} else {
								echo $result['apiMessages'];
							}

						}
						break;
					case 'Pollsopinion':

						$platformParams = Utils::decodeParam(json_decode($platform['params'], true));
						$curl = curl_init();
						$akey = $platformParams['app_key'];
						$aid = $platformParams['app_id'];
						$protocol = 'http';
						if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
							$protocol = 'https';
						}
						$domainName = $_SERVER['HTTP_HOST'];
						$url = $protocol . '://' . $domainName;
						curl_setopt_array($curl, [
							CURLOPT_URL => $platform['platform_click_url'],
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'POST',
							CURLOPT_POSTFIELDS => json_encode([
								"surveyID" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no'],
								"SuccessLink" => $url . '/api/index/callback?platform=Pollsopinion&uid={{panellist_id}}&status=C',
								"disQualifiedLink" => $url . '/api/index/callback?platform=Pollsopinion&uid={{panellist_id}}&status=S',
								"TermLink" => $url . '/api/index/callback?platform=Pollsopinion&uid={{panellist_id}}&status=T',
								"OverQuotaLink" => $url . '/api/index/callback?platform=Pollsopinion&uid={{panellist_id}}&status=Q',
								"useStaticLink" => 0
							]),
							CURLOPT_HTTPHEADER => [
								"Accept: application/json",
								"Authorization: $akey",
								"payload: $aid",
								"Content-Type: application/json"
							],
						]);
						$response = curl_exec($curl);
						$err = curl_error($curl);
						curl_close($curl);
						$logData = "================ POLLSOPINION LINK LOG ================\n";
						$logData .= "Time: " . date('Y-m-d H:i:s') . "\n";
						$logData .= "Project: " . ($project['project_pno'] ?? 'N/A') . " (ID: " . ($project['project_no'] ?? 'N/A') . ")\n";
						$logData .= "URL: " . $platform['platform_click_url'] . "\n";
						$logData .= "Headers: Authorization: $akey | payload: $aid\n";
						$logData .= "Payload: " . json_encode([
							"surveyID" => is_numeric($project['project_no']) ? (int)$project['project_no'] : $project['project_no'],
							"SuccessLink" => $url . '/api/index/callback?platform=Pollsopinion&uid={{panellist_id}}&status=C',
							"disQualifiedLink" => $url . '/api/index/callback?platform=Pollsopinion&uid={{panellist_id}}&status=S',
							"TermLink" => $url . '/api/index/callback?platform=Pollsopinion&uid={{panellist_id}}&status=T',
							"OverQuotaLink" => $url . '/api/index/callback?platform=Pollsopinion&uid={{panellist_id}}&status=Q',
							"useStaticLink" => 0
						]) . "\n";
						$logData .= "Response: " . $response . "\n";
						$logData .= "Error: " . $err . "\n";
						$logData .= "=======================================================\n\n";
						file_put_contents(public_path() . 'api_debug.log', $logData, FILE_APPEND);

						if ($err) {
							echo "Jump failed, please try again";
						} else {
							$result = json_decode($response, true);
							if ($result['apiStatus'] == 1) {
								$pos = strpos($result['surveyInfo'][0]['SurveyEntryUrl'], '&pid=');
								$project_click_url = substr($result['surveyInfo'][0]['SurveyEntryUrl'], 0, $pos) . '&pid=' . $uuid;
								echo '<script>window.location.href="' . $project_click_url . '";</script>';
							} else {
								echo $result['apiMessages'];
							}

						}
						break;
					default:
						echo '<script>window.location.href="' . $project['project_click_url'] . $uuid . '";</script>';
						break;
				}
			} else {
				return 'uuid generation failed, please try again';
			}
		} else {
			return 'platform or project does not exist or is not open';
		}
	}
}