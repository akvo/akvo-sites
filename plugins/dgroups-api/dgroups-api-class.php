<?php


class DgroupsApi{
    public function __construct() {
        global $dgroups_api_plugin_options;
    }
    
    public static function getData($forceRefresh = false){
        global $dgroups_api_plugin_options;
        $sDataFileName = plugin_dir_path(__FILE__).'api_data.json';
        if(!$forceRefresh){
            $sCachedData = file_get_contents($sDataFileName);
            if($sCachedData!==''){
                $oCachedData = json_decode($sCachedData);
                if(strtotime('-1 hour') < $oCachedData->timestamp){
                    return (array)$oCachedData->data;
                }
            }
        }
        $sApiKey = $dgroups_api_plugin_options['api_key'];
        $sApiSecret = $dgroups_api_plugin_options['api_secret'];
        $sPath = '/rwsn/'.$dgroups_api_plugin_options['path'].'/__api/v2/stats/basic';
        $sTimestamp = file_get_contents('https://dgroups.org/rwsn/'.$dgroups_api_plugin_options['path'].'/__api/v2/time');
        $sHash = sha1($sPath.$sApiKey.$sApiSecret.$sTimestamp);
        $sUrl = 'https://dgroups.org'.$sPath;
        $crl = curl_init();

        $headr = array();
        $headr[] = 'Accept: application/json';
        $headr[] = 'Path: '.$sPath;
        $headr[] = 'Authorization: '.$sHash;
        $headr[] = 'X-ECS-Api-Key: '.$sApiKey;
        $headr[] = 'X-ECS-Api-RequestTime: '.$sTimestamp;
        curl_setopt($crl, CURLOPT_URL, $sUrl);
        curl_setopt($crl, CURLOPT_HEADER, false);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
        $rest = @curl_exec($crl);

        curl_close($crl);
        $apiresult = json_decode($rest,true);
        $aLog = array(
            'timestamp' => strtotime($sTimestamp),
            'data' => $apiresult
        );
        $sLog = json_encode($aLog);
        file_put_contents($sDataFileName, $sLog);
        return $apiresult;
    }
}
