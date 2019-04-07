<?php

class ConfigurationManager {

    private $globalConfig;
    private $mainConfig;

    public function __construct() {
        
    }

    public function getTenantConfiguration(){
        if (!isset($this->globalConfig)){
            $this->globalConfig = $this->getGlobalConfiguration();
        }

        $tenantConfig = TENANT_RESOURCE_PATH . "/tenant.yml";
        $tenantConfigData = yaml_parse_file($tenantConfig);

        return $this->mergeConfiguration($tenantConfigData);
    }

    public function getGlobalConfiguration(){

    }

    public function getMainConfiguration(){
        $mainConfigFile = BASE_PATH . "/config_global.yml";
        
        if (file_exists($mainConfigFile)){
            $mainConfigData = yaml_parse_file($mainConfigFile);
            if (isset ($mainConfigData["global_constants"])) {
                foreach ($mainConfigData["global_constants"] as $key => $value) {
                    define ($key, $value);
                }
            }

            if (defined("DISTRIBUTION_PATH")){
                $distConfigFile = DISTRIBUTION_PATH . "/distribution.yml";
                if (file_exists($distConfigFile)){
                    $distConfigData = yaml_parse_file($distConfigFile);
                    $this->mainConfig = Helpers::deepCopy($mainConfigData, $distConfigData);
                }
            }

            if (!isset($this->mainConfig)){
                $this->mainConfig = $mainConfigData;
            }
            
        }else {
            $this->mainConfig = array();
        }
        
        define ("UNIT_PATH", BASE_PATH. "/units");
        $multiTenancy = Helpers::getValueByPath($this->mainConfig, "config.multiTenancy", true);

        if (defined("DISTRIBUTION_PATH")){
            if ($multiTenancy === true){
                define ("TENANT_RESOURCE_PATH", DISTRIBUTION_PATH. "/domains/$_SERVER[HTTP_HOST]");
            }else {
                define ("TENANT_RESOURCE_PATH", DISTRIBUTION_PATH. "/domains/localhost");
            }
            
            define ("DISTRIBUTION_UNIT_PATH", DISTRIBUTION_PATH . "/units");
        }else {
            
            if ($multiTenancy === true){
                define ("TENANT_RESOURCE_PATH", BASE_PATH. "/domains/$_SERVER[HTTP_HOST]");
            } else {
                define ("TENANT_RESOURCE_PATH", BASE_PATH. "/domains/localhost");
            }
        }
    }

    private function mergeConfiguration($tenantConfig){
        //inherit global configuration
        return $tenantConfig;
    }

}