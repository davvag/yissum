<?php

define ("BASE_PATH", dirname(__FILE__));
define ("CORE_PATH", BASE_PATH . "/core");

require_once (CORE_PATH. "/configuration_manager.php");
require_once (CORE_PATH. "/route_manager.php");
require_once (CORE_PATH. "/dispatcher.php");
require_once (CORE_PATH. "/urnresolver.php");
require_once (CORE_PATH. "/abstract_unit.php");
require_once (CORE_PATH. "/invoke_source.php");
require_once (CORE_PATH. "/context.php");
require_once (CORE_PATH. "/event_manager.php");
require_once (CORE_PATH. "/helpers.php");

class DavvagApiManager {
    
    public static $routeManager;
    public static $configurationManager;
    public static $mainConfig;
    public static $dispatcher;
    public static $resolver;
    public static $tenantConfiguration;
    public static $eventManager;

    public static function start(){
        DavvagApiManager::$configurationManager = new ConfigurationManager();
        DavvagApiManager::$routeManager = new RouteManager();
        DavvagApiManager::$dispatcher = new Dispatcher();
        DavvagApiManager::$resolver = new UrnResolver();
        DavvagApiManager::$mainConfig = DavvagApiManager::$configurationManager->getMainConfiguration();
        DavvagApiManager::$tenantConfiguration = DavvagApiManager::$configurationManager->getTenantConfiguration();
        DavvagApiManager::$eventManager = new EventManager();

        DavvagApiManager::executeDistributionEntryPoint();
        
        try{
            DavvagApiManager::$routeManager->loadTenantRoutes();
        }catch(Exception $e){
            $err =new stdClass();
            $err->success=false;
            $err->message=$e->getMessage();
            header("HTTP/1.1 501 ERROR");
            header("content-type: application/json");
            print_r(json_encode($err));
        }

    }

    private static function executeDistributionEntryPoint(){
        if (defined("DISTRIBUTION_PATH")){
            $entryFileName = DISTRIBUTION_PATH . "/distribution.php";
            if (file_exists($entryFileName)){
                require_once($entryFileName);
            }
        }
    }

    public static function addAction($action, $handler){
        DavvagApiManager::$eventManager->addAction($action, $handler);
    }

    public static function addFilter($filter, $handler){
        DavvagApiManager::$eventManager->addFilter($filter, $handler);
    }

    public static function triggerAction($action, $data = null){
        DavvagApiManager::$eventManager->triggerAction($action, $data);
    }

    public static function triggerFilter($filter, $data = null){
        DavvagApiManager::$eventManager->triggerFilter($filter, $data);
    }
}