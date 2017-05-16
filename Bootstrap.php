<?php

use Shopware\CustomModels\DotmailerEmailMarketing\DotmailerEmailMarketing;

/**
 * The Bootstrap class is the main entry point of any shopware plugin.
 *
 * Short function reference
 * - install: Called a single time during (re)installation. Here you can trigger install-time actions like
 *   - creating the menu
 *   - creating attributes
 *   - creating database tables
 *   You need to return "true" or array('success' => true, 'invalidateCache' => array()) in order to let the installation
 *   be successful
 *
 * - update: Triggered when the user updates the plugin. You will get passes the former version of the plugin as param
 *   In order to let the update be successful, return "true"
 *
 * - uninstall: Triggered when the plugin is reinstalled or uninstalled. Clean up your tables here.
 */
class Shopware_Plugins_Backend_DotmailerEmailMarketing_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'plugin.json'), true);
        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getLabel()
    {
        return 'dotmailer Email Marketing';
    }

    public function uninstall()
    {
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('Shopware\CustomModels\DotmailerEmailMarketing\DotmailerEmailMarketing')
        );
        $tool->dropSchema($classes);

        return true;
    }

    public function update($oldVersion)
    {
        return true;
    }

    public function install()
    {
        if (!$this->assertMinimumVersion('4.3.0')) {
            throw new \RuntimeException('At least Shopware 4.3.0 is required');
        }

        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopStartup',
            'onStartDispatch'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_Index',
            'addTemplateDir'
        );

        $this->updateSchema();

        return array('success' => true, 'invalidateCache' => array('frontend', 'backend'));
    }

    public function addTemplateDir(Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        if ($view->hasTemplate()) {
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->extendsTemplate('backend/dotmailer_email_marketing/menuitem.tpl');
        }
    }

    /**
     * Enable plugin method
     *
     * @return bool
     */
    public function enable()
    {
        $em = $this->Application()->Models();
        $plugin_id = $em->find('Shopware\CustomModels\DotmailerEmailMarketing', 1)->plugin_id;

        if ($plugin_id === null) {
            require __DIR__ . '\Models\DotmailerEmailMarketing\DotmailerEmailMarketing.php';
            $dotmailer_email_marketing = new DotmailerEmailMarketing();

            $length = 128;
            $crypto_strong = true;
            $dotmailer_email_marketing
                ->setPluginID( bin2hex( openssl_random_pseudo_bytes( $length, $crypto_strong ) ) );

            $em->persist($dotmailer_email_marketing);
            $em->flush();

            $plugin_id = $dotmailer_email_marketing->plugin_id;
        }

        $store = $em->find('Shopware\Models\Shop\Shop', 1);
        $store_url = $store->host . $store->base_path;
        $bridge_url = $store_url . '/bridge2cart/bridge.php';
        $store_root = $_SERVER['DOCUMENT_ROOT'] . $store->base_path;

        $url = "https://debug-webapp.dotmailer.internal/shopware/connect?storename=$store->name&storeurl=$store_url&bridgeUrl=$bridge_url&storeroot=&pluginID=$plugin_id";

        $this->createMenuItem(
            array(
            'label' => 'dotmailer Email Marketing',
            'onclick' => 'window.open(' . "$url" . ', "_blank");',
            'class' => 'sprite-dotmailer-email-marketing',
            'active' => 1,
            'parent' => $this->Menu()->findOneBy(['label' => 'Marketing'])
            )
        );
        
        $post = curl_init("http://debug-tracking.dotmailer.internal/e/shopware/enable?pluginid=$plugin_id");
        curl_exec($post);
        
        return true;
    }

    /**
     * Disable plugin method
     *
     * @return bool
     */
    public function disable()
    {
        return !empty($this->info->capabilities['enable']);
    }

    /**
     * Creates the database scheme from an existing doctrine model.
     *
     * Will remove the table first, so handle with care.
     */
    protected function updateSchema()
    {
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('Shopware\CustomModels\DotmailerEmailMarketing\DotmailerEmailMarketing')
        );

        try {
            $tool->dropSchema($classes);
        } catch (Exception $e) {
            //ignore
        }
        $tool->createSchema($classes);
    }

    /**
     * This callback function is triggered at the very beginning of the dispatch process and allows
     * us to register additional events on the fly. This way you won't ever need to reinstall you
     * plugin for new events - any event and hook can simply be registered in the event subscribers
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $this->registerMyComponents();
        $this->registerCustomModels();
        $this->registerMyTemplateDir();
        $this->registerMySnippets();
    }

    /**
     * Registers the template directory, needed for templates in frontend an backend
     */
    public function registerMyTemplateDir()
    {
        Shopware()->Template()->addTemplateDir($this->Path() . 'Views');
    }

    /**
     * Registers the snippet directory, needed for backend snippets
     */
    public function registerMySnippets()
    {
        $this->Application()->Snippets()->addConfigDir(
            $this->Path() . 'Snippets/'
        );
    }

    public function registerMyComponents()
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\DotmailerEmailMarketing',
            $this->Path()
        );
    }
}
