<?php
declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\model\platform\PlatformModel;
use app\common\model\platform\CurrencyModel;
use app\common\model\project\ProjectModel;
use app\common\service\utils\Utils;
use app\common\service\platform\PlatformService;
use thirdsdk\Http;

class Pull extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('pull')
            ->addArgument('type', Argument::REQUIRED)
            ->setDescription('the pull command');
    }

    protected function execute(Input $input, Output $output)
    {
        $type = $input->getArgument('type');
        if ($type == 'all') {
            $platforms = PlatformService::all();
        } else {
            $platformModel = new PlatformModel();
            $platforms[] = $platformModel->where('platform_sign', $type)->find();
        }
        foreach ($platforms as $platform) {
            $platformParams = $platform['params'] ? Utils::decodeParam(json_decode($platform['params'], true)) : [];
            $projectModel = new ProjectModel();
            $currencyModel = new CurrencyModel();
            $currency = $currencyModel->where('currency_id', $platform['platform_currency'])->find();
            if ($platform['platform_sign'] == 'Notik') {
                $update = delete_update();
                $projectModel->where('platform_id', $platform['platform_id'])->where('is_api',1)->update($update);
                $param = http_build_query([
                    'api_key' => $platformParams['app_key'],
                    'pub_id' => $platformParams['pub_id'],
                    'app_id' => $platformParams['app_id'],
                ]);
                $url = $platform['platform_url'].'?'.$param;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($response, true);
                if ($result && $result['code'] == 200) {
                    $data = [];
                    foreach ($result['offers']['data'] as $offer) {
                        if (in_array('Surveys', $offer['categories'])) {
                            $row = [
                                'platform_id' => $platform['platform_id'],
                                'project_sign' => md5($platform['platform_id'] . $offer['offer_id'] . implode(',', $offer['country_code'])),
                                'project_pno' => general_pno(),
                                'project_no' => $offer['offer_id'],
                                'project_name' => $offer['name'],
                                'project_code' => implode(',', $offer['country_code']),
                                'project_cpi' => $offer['payout'],
                                'project_click_url' => $offer['click_url'],
                                'content' => $offer['description1'] . '<br />' . $offer['description2'] . '<br />' . $offer['description3'],
                                'project_currency' => $currency['currency_id'],
                                'is_api' => 1,
                            ];
                            $data[] = $row;
                        }
                    }
                    if ($result['offers']['has_more_pages']) {
                        $this->pull_next_notik($platform, $result['offers']['next_page_url'], $currency);
                    }
                    if (count($data)) {
                        foreach ($data as $item) {
                            $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                            if ($project) {
                                unset($item['project_pno']);
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $item['delete_uid'] = 0;
                                $item['is_delete'] = 0;
                                $item['delete_time'] = NULL;
                                $project->save($item);
                            } else {
                                $item['create_uid'] = user_id();
                                $item['create_time'] = datetime();
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $projectModel->create($item);
                            }
                        }
                    }
                }

            }
            if ($platform['platform_sign']=='Pollsopinion'){
                $update = delete_update();
                $projectModel->where('platform_id', $platform['platform_id'])->where('is_api',1)->update($update);
                $curl = curl_init();
                $apid = $platformParams['app_id'];
                $akey = $platformParams['app_key'];
                curl_setopt_array($curl, [
                    CURLOPT_URL => $platform['platform_url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "Accept: application/json",
                        "Authorization: $akey",
                        "payload: $apid"
                    ],
                ]);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                file_put_contents(__DIR__.'/poll_callback.txt',__FUNCTION__.':'.date('Y-m-d H:i:s')."\n",8);
                file_put_contents(__DIR__.'/poll_callback.txt',$response."\n\n",8);
                if (!$err) {
                    $result = json_decode($response, true);
                    $data = [];
                    foreach ($result['surveys'] as $offer) {
                        $row = [
                            'platform_id' => $platform['platform_id'],
                            'project_sign' => md5($platform['platform_id'] . $offer['surveyID'] . $offer['countrieISOcode']),
                            'project_pno' => general_pno(),
                            'project_no' => $offer['surveyID'],
                            'project_name' => $offer['surveyID'],
                            'project_code' => $offer['countrieISOcode'],
                            'project_cpi' => $offer['surveyCPI'],
                            'project_loi' => $offer['LOI'],
                            'project_ir' => $offer['IR'],
                            'project_quota' => $offer['surveyTargetCount'],
                            'project_currency' => $currency['currency_id'],
                            'is_api' => 1,
                        ];
                        $data[] = $row;
                    }
                    if (count($data)) {
                        foreach ($data as $item) {
                            $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                            if ($project) {
                                unset($item['project_pno']);
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $item['delete_uid'] = 0;
                                $item['is_delete'] = 0;
                                $item['delete_time'] = NULL;
                                $project->save($item);
                            } else {
                                $item['create_uid'] = user_id();
                                $item['create_time'] = datetime();
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $projectModel->create($item);
                            }
                        }
                    }
                }
            }
            if ($platform['platform_sign'] == 'Innovatemr') {
                $update = delete_update();
                $projectModel->where('platform_id', $platform['platform_id'])->where('is_api',1)->update($update);
                $curl = curl_init();
                $akey = $platformParams['app_key'];
                curl_setopt_array($curl, [
                    CURLOPT_URL => $platform['platform_url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
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
                if (!$err) {
                    $result = json_decode($response, true);
                    $data = [];
                    foreach ($result['result'] as $offer) {
                        $row = [
                            'platform_id' => $platform['platform_id'],
                            'project_sign' => md5($platform['platform_id'] . $offer['surveyId'] . $offer['CountryCode']),
                            'project_pno' => general_pno(),
                            'project_no' => $offer['surveyId'],
                            'project_name' => $offer['surveyName'],
                            'project_code' => $offer['CountryCode'],
                            'project_cpi' => $offer['CPI'],
                            'project_loi' => $offer['LOI'],
                            'project_ir' => $offer['IR'],
                            'project_click_url' => str_replace("[%%pid%%]", '', $offer['entryLink']),
                            'project_currency' => $currency['currency_id'],
                            'project_params' => json_encode([['key'=>time(),'field'=>'remainingN','name'=> 'remaining','value'=> $offer['remainingN']]]),
                            'is_api' => 1,
                        ];
                        $data[] = $row;
                    }
                    if (count($data)) {
                        foreach ($data as $item) {
                            $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                            if ($project) {
                                unset($item['project_pno']);
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $item['delete_uid'] = 0;
                                $item['is_delete'] = 0;
                                $item['delete_time'] = NULL;
                                $project->save($item);
                            } else {
                                $item['create_uid'] = user_id();
                                $item['create_time'] = datetime();
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $projectModel->create($item);
                            }
                        }
                    }
                }
            }
            if ($platform['platform_sign'] == 'MarketXcel') {
                $update = delete_update();
                $projectModel->where('platform_id', $platform['platform_id'])->where('is_api',1)->update($update);
                $curl = curl_init();
                $apid = $platformParams['app_id'];
                $akey = $platformParams['app_token'];
                curl_setopt_array($curl, [
                    CURLOPT_URL => $platform['platform_url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "Accept: application/json",
                        "token: $akey",
                        "supplierid:$apid"
                    ],
                ]);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if (!$err) {
                    $result = json_decode($response, true);
                    
                    if ($result && $result['ResponseCode'] == 1) {
                        $data = [];
                        foreach ($result['Data'] as $offer) {
                            if($offer['cpi'] && $offer['cpi'] > 0){
                                $row = [
                                'platform_id' => $platform['platform_id'],
                                'project_sign' => md5($platform['platform_id'] . $offer['project_code'] . $offer['country']),
                                'project_pno' => general_pno(),
                                'project_no' => $offer['project_code'],
                                'project_name' => $offer['project_name'],
                                'project_code' => $offer['country'],
                                'project_cpi' => $offer['cpi'],
                                'project_loi' => $offer['project_loi'],
                                'project_ir' => $offer['project_ir'],
                                'project_click_url' => $offer['live_url'],
                                'project_currency' => $currency['currency_id'],
                                'project_content' => $offer['target_spec'],
                                'is_api' => 1,
                                ];
                                $data[] = $row;
                            }
                        }
                    }
                    if (count($data)) {
                        foreach ($data as $item) {
                            $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                            if ($project) {
                                unset($item['project_pno']);
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $item['delete_uid'] = 0;
                                $item['is_delete'] = 0;
                                $item['delete_time'] = NULL;
                                $project->save($item);
                            } else {
                                $item['create_uid'] = user_id();
                                $item['create_time'] = datetime();
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $projectModel->create($item);
                            }
                        }
                    }
                }
            }
            if ($platform['platform_sign'] == 'Gowebsurveys') {
                $update = delete_update();
                $projectModel->where('platform_id', $platform['platform_id'])->where('is_api',1)->update($update);
                $curl = curl_init();
                $akey = $platformParams['app_key'];
                $aid = $platformParams['app_id'];
                curl_setopt_array($curl, [
                    CURLOPT_URL => $platform['platform_url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "Accept: application/json",
                        "Authorization: $akey",
                        "payload: $aid"
                    ],
                ]);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                //file_put_contents(__DIR__.'/Gowebsurveys_callback.txt',__FUNCTION__.':'.date('Y-m-d H:i:s')."\n",8);
                //file_put_contents(__DIR__.'/Gowebsurveys_callback.txt',$response."\n\n",8);
                curl_close($curl);
                if ($err) {
                    return error('Pull failed');
                } else {
                    $result = json_decode($response, true);
                    if ($result) {
                        $data = [];
                        foreach ($result['surveys'] as $offer) {
                            $row = [
                                'platform_id' => $platform['platform_id'],
                                'project_sign' => md5($platform['platform_id'] . $offer['surveyID'] . $offer['countrieISOcode']),
                                'project_pno' => general_pno(),
                                'project_no' => $offer['surveyID'],
                                'project_name' => $offer['surveyID'],
                                'project_code' => $offer['countrieISOcode'],
                                'project_cpi' => $offer['surveyCPI'],
                                'project_loi' => $offer['LOI'],
                                'project_ir' => $offer['IR'],
                                'project_quota' => $offer['surveyTargetCount'],
                                'project_click_url' => '',
                                'project_currency' => $currency['currency_id'],
                                'project_content' => '',
                                'is_api' => 1,
                            ];
                            $data[] = $row;
                        }
                        if (count($data)) {
                            foreach ($data as $item) {
                                $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                                if ($project) {
                                    unset($item['project_pno']);
                                    $item['update_uid'] = user_id();
                                    $item['update_time'] = datetime();
                                    $item['delete_uid'] = 0;
                                    $item['is_delete'] = 0;
                                    $item['delete_time'] = NULL;
                                    $project->save($item);
                                } else {
                                    $item['create_uid'] = user_id();
                                    $item['create_time'] = datetime();
                                    $item['update_uid'] = user_id();
                                    $item['update_time'] = datetime();
                                    $projectModel->create($item);
                                }
                            }
                        }
                    }
                }
            }
            if ($platform['platform_sign'] == 'mirat') {
                $update = delete_update();
                $projectModel->where('platform_id', $platform['platform_id'])->where('is_api',1)->update($update);
                $page = 1;
                $this->getMirat($platform,$platformParams,$projectModel,$currency,$page);
            }
            if ($platform['platform_sign'] == 'Innomoment') {
                $update = delete_update();
                $projectModel->where('platform_id', $platform['platform_id'])->where('is_api',1)->update($update);
                $url = $platform['platform_url'].'?mid=' . $platformParams['app_id'] . '&sign=' . md5($platformParams['app_id'] . ':' . $platformParams['app_key']);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($response, true);
                file_put_contents(__DIR__.'/Innomoment_callback.txt',__FUNCTION__.':'.date('Y-m-d H:i:s')."\n",8);
                file_put_contents(__DIR__.'/Innomoment_callback.txt',$response."\n\n",8);
                if ($result) {
                    $data = [];
                    foreach ($result['data'] as $offer) {
                        $row = [
                            'platform_id' => $platform['platform_id'],
                            'project_sign' => md5($platform['platform_id'] . $offer['pno'] . $offer['country']),
                            'project_pno' => general_pno(),
                            'project_no' => $offer['pno'],
                            'project_name' => $offer['title'],
                            'project_code' => $offer['country'],
                            'project_cpi' => $offer['price'],
                            'project_click_url' => $offer['url'],
                            'project_ir' => $offer['ir'],
                            'project_loi' => $offer['minLoi'].'-'.$offer['maxLoi'],
                            'project_currency' => $currency['currency_id'],
                            'project_content' => $offer['quotaUrl'],
                            'is_api' => 1,
                        ];
                        $data[] = $row;
                    }
                    if (count($data)) {
                        foreach ($data as $item) {
                            $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                            if ($project) {
                                unset($item['project_pno']);
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $item['delete_uid'] = 0;
                                $item['is_delete'] = 0;
                                $item['delete_time'] = NULL;
                                $project->save($item);
                            } else {
                                $item['create_uid'] = user_id();
                                $item['create_time'] = datetime();
                                $item['update_uid'] = user_id();
                                $item['update_time'] = datetime();
                                $projectModel->create($item);
                            }
                        }
                    }
                }

            }
            if ($platform['platform_sign'] == 'Zamplia') {
                $update = delete_update();
                $projectModel->where('platform_id', $platform['platform_id'])->where('is_api',1)->update($update);
                $curl = curl_init();
                $akey = $platformParams['app_key'];
                curl_setopt_array($curl, [
                    CURLOPT_URL => $platform['platform_url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "Accept: application/json",
                        "ZAMP-KEY: $akey",
                    ],
                ]);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    return error('Pull failed');
                } else {
                    $result = json_decode($response, true);
                    if ($result && $result['success']) {
                        $data = [];
                        foreach ($result['result']['data'] as $offer) {
                            $row = [
                                'platform_id' => $platform['platform_id'],
                                'project_sign' => md5($platform['platform_id'] . $offer['SurveyId'] . $offer['LanguageCode']),
                                'project_pno' => general_pno(),
                                'project_no' => $offer['SurveyId'],
                                'project_name' => $offer['Name'],
                                'project_code' => $offer['LanguageCode'],
                                'project_cpi' => $offer['CPI'],
                                'project_loi' => $offer['LOI'],
                                'project_ir' => $offer['IR'],
                                'project_click_url' => '',
                                'project_currency' => $currency['currency_id'],
                                'project_content' => '',
                                'is_api' => 1
                            ];
                            $data[] = $row;
                        }
                        if (count($data)) {
                            foreach ($data as $item) {
                                $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                                if ($project) {
                                    unset($item['project_pno']);
                                    $item['update_uid'] = user_id();
                                    $item['update_time'] = datetime();
                                    $item['delete_uid'] = 0;
                                    $item['is_delete'] = 0;
                                    $item['delete_time'] = NULL;
                                    $project->save($item);
                                } else {
                                    $item['create_uid'] = user_id();
                                    $item['create_time'] = datetime();
                                    $item['update_uid'] = user_id();
                                    $item['update_time'] = datetime();
                                    $projectModel->create($item);
                                }
                            }
                        }
                    }
                }

            }

        }
    }
    private function getMirat($platform,$platformParams,$projectModel,$currency,$page){
        $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $platform['platform_url'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode([
                        'authorizationKey' => $platformParams['app_key'],
                        'country' => 'US',
                        'rows' => 500,
                        'page' => $page,
                        'language' => 'English',
                        'CPIGTE' => 0.8,
                  ]),
                  CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "publisher-authentication-key: ".$platformParams['app_secret']
                  ],
                ]);
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                $result = json_decode($response, true);
                if ($result && $result['success']) {
                    $data = [];
                    $totalPages = $result['pagination']['totalPages'];
                    foreach ($result['surveys'] as $offer) {
                            $row = [
                                'platform_id' => $platform['platform_id'],
                                'project_sign' => md5($platform['platform_id'] . $offer['survey_number'] . $offer['countryCode']),
                                'project_pno' => general_pno(),
                                'project_no' => $offer['survey_number'],
                                'project_name' => $result['projectName'],
                                'project_code' => $offer['countryCode'],
                                'project_cpi' => $offer['cpi'],
                                'project_loi' => $offer['loi'],
                                'project_ir' => $offer['ir'],
                                'project_quota' => $offer['required_completes'],
                                'project_click_url' => '',
                                'project_currency' => $currency['currency_id'],
                                'project_content' => ''
                            ];
                            $data[] = $row;
                    }
                    if (count($data)) {
                        foreach ($data as $item) {
                            $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                            if ($project) {
                                    unset($item['project_pno']);
                                    $item['update_uid'] = user_id();
                                    $item['update_time'] = datetime();
                                    $item['delete_uid'] = 0;
                                    $item['is_delete'] = 0;
                                    $item['delete_time'] = NULL;
                                    $project->save($item);
                            } else {
                                    $item['create_uid'] = user_id();
                                    $item['create_time'] = datetime();
                                    $item['update_uid'] = user_id();
                                    $item['update_time'] = datetime();
                                    $projectModel->create($item);
                            }
                        }
                    }
                    if($totalPages > $page){
                        $page = $page + 1;
                        $this->getMirat($platform,$platformParams,$projectModel,$currency,$page);
                    }
                }
    }
    private function pull_next_notik($platform, $url, $currency)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);
        $projectModel = new ProjectModel();
        if ($result && $result['code'] == 200) {
            $data = [];
            foreach ($result['offers']['data'] as $offer) {
                if (in_array('Surveys', $offer['categories'])) {
                    $row = [
                        'platform_id' => $platform['platform_id'],
                        'project_sign' => md5($platform['platform_id'] . $offer['offer_id'] . implode(',', $offer['country_code'])),
                        'project_pno' => general_pno(),
                        'project_no' => $offer['offer_id'],
                        'project_name' => $offer['name'],
                        'project_code' => implode(',', $offer['country_code']),
                        'project_cpi' => $offer['payout'],
                        'project_click_url' => $offer['click_url'],
                        'content' => $offer['description1'] . '<br />' . $offer['description2'] . '<br />' . $offer['description3'],
                        'project_currency' => $currency['currency_id'],
                        'is_api' => 1,
                    ];
                    $data[] = $row;
                }
            }
            if (count($data)) {
                foreach ($data as $item) {
                    $project = $projectModel->where('project_sign', $item['project_sign'])->find();
                    if ($project) {
                        unset($item['project_pno']);
                        $item['update_uid'] = user_id();
                        $item['update_time'] = datetime();
                        $item['delete_uid'] = 0;
                        $item['is_delete'] = 0;
                        $item['delete_time'] = NULL;
                        $project->save($item);
                    } else {
                        $item['create_uid'] = user_id();
                        $item['create_time'] = datetime();
                        $item['update_uid'] = user_id();
                        $item['update_time'] = datetime();
                        $projectModel->create($item);
                    }
                }
            }
            if ($result['offers']['has_more_pages']) {
                $this->pull_next_notik($platform, $result['offers']['next_page_url'], $currency);
            }
        }
    }
}
