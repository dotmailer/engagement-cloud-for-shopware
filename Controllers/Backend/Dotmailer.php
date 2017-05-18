<?php

use Shopware\Components\CSRFWhitelistAware;

/**
 * Backend controllers extending from Shopware_Controllers_Backend_Application do support the new backend components
 */
class Shopware_Controllers_Backend_Dotmailer extends Shopware_Controllers_Backend_Application implements CSRFWhitelistAware
{
    protected $model = 'DotmailerEmailMarketing';
    protected $alias = 'dotmailer_email_marketing';

    public function connectAction()
    {
        $em = $this->getManager();
        $store = $em->find('Shopware\Models\Shop\Shop', 1);
        $settings = $em->find('Shopware\CustomModels\DotmailerEmailMarketing\DotmailerEmailMarketing', 1);

        $plugin_id = $settings !== null ? $settings->getPluginID() : die();
        
        $connection_query = http_build_query(
            array(
                'storename' => $store->getName(),
                'storeurl' => $store->getHost() . $store->getBasePath(),
                'bridgeurl' => $store->getHost() . $store->getBasePath() . '/bridge2cart/bridge.php',
                'storeroot' => $_SERVER['DOCUMENT_ROOT'] . $store->getBasePath(),
                'pluginid' => $plugin_id)
        );

        $this->redirect(Shopware_Plugins_Backend_DotmailerEmailMarketing_Bootstrap::getWebAppUrl() . '/shopware/connect?' . $connection_query);
    }

    public function getWhitelistedCSRFActions()
    {
        return [
            'connect'
        ];
    }
}
