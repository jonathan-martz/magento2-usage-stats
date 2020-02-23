<?php

namespace JonathanMartz\UsageStats\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Ping
 * @package JonathanMartz\UsageStats\Cron
 */
class Ping
{
    const ENABLE = 'useagestats/general/enable';
    const NAMES = 'useagestats/general/names';
    const ENDPOINT = 'useagestats/general/endpoint';

    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getNamespaces(): array
    {
        return explode(',', $this->scopeConfig->getValue(self::NAMES));
    }

    public function getEndpoint(): string
    {
        return (string)$this->scopeConfig->getValue(self::ENDPOINT);
    }

    public function isActive(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::ENABLE);
    }

    public function getModuleList(): array
    {
        return [];
    }

    public function ping(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getEndpoint());
        curl_setopt($ch, CURLOPT_USERAGENT, 'Its me Mario ;)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->getModuleList()));
        curl_exec($ch);
        curl_close($ch);
    }

    public function execute()
    {
        if($this->isActive()){
            $this->ping();
        }
    }
}
