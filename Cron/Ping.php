<?php

namespace JonathanMartz\UsageStats\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\FullModuleList;

/**
 * Class Ping
 * @package JonathanMartz\UsageStats\Cron
 */
class Ping
{
    const ENABLE = 'useagestats/general/enable';
    const NAME = 'useagestats/general/name';
    const ENDPOINT = 'useagestats/general/endpoint';

    private $scopeConfig;

    private $moduleList;

    public function __construct(ScopeConfigInterface $scopeConfig, FullModuleList $moduleList)
    {
        $this->scopeConfig = $scopeConfig;
        $this->moduleList = $moduleList;
    }

    public function getNamespace()
    {
        return (string)$this->scopeConfig->getValue(self::NAME);
    }

    public function getEndpoint()
    {
        return (string)$this->scopeConfig->getValue(self::ENDPOINT);
    }

    public function isActive()
    {
        return (bool)$this->scopeConfig->getValue(self::ENABLE);
    }

    public function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    public function collectModules()
    {
        $modules = $this->moduleList->getAll();
        $tmp = [];

        foreach($modules as $key => $value) {
            if($this->startsWith($key, 'JonathanMartz')) {
                var_dump($key);

                // version
                $tmp[] = $key;

            }
        }


        return $tmp;
    }

    public function getModuleList()
    {
        $modules = $this->collectModules();

        return $modules;
    }

    public function ping()
    {
        $ch = curl_init();
        $json = json_encode($this->getModuleList(), JSON_FORCE_OBJECT);
        curl_setopt($ch, CURLOPT_URL, $this->getEndpoint());
        curl_setopt($ch, CURLOPT_USERAGENT, 'Its me Mario ;)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $output = curl_exec($ch);
        var_dump($output);
        var_dump($json);
        curl_close($ch);
    }

    public function execute()
    {
        if($this->isActive()){
            $this->ping();
        }
    }
}
