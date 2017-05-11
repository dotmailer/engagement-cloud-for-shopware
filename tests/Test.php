<?php

class PluginTest extends Shopware\Components\Test\Plugin\TestCase
{
    protected static $ensureLoadedPlugins = array(
        'dotmailerEmailMarketing' => array(
        )
    );

    public function setUp()
    {
        parent::setUp();

        $helper = \TestHelper::Instance();
        $loader = $helper->Loader();


        $pluginDir = getcwd() . '/../';

        $loader->registerNamespace(
            'Shopware\\dotmailerEmailMarketing',
            $pluginDir
        );
    }

    public function testCanCreateInstance()
    {
        /** @var Shopware_Plugins_Frontend_dotmailerEmailMarketing_Bootstrap $plugin */
        $plugin = Shopware()->Plugins()->Frontend()->dotmailerEmailMarketing();

        $this->assertInstanceOf('Shopware_Plugins_Frontend_dotmailerEmailMarketing_Bootstrap', $plugin);
    }
}