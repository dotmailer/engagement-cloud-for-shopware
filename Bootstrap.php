<?php

use Shopware\CustomModels\DotmailerEmailMarketing\DotmailerEmailMarketing;

/**
 * The Bootstrap class is the main entry point of any shopware plugin.
 */
class Shopware_Plugins_Backend_DotmailerEmailMarketing_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public static function getWebAppUrl()
    {
        return 'https://login.dotdigital.com';
    }

    public static function getTrackingSiteUrl()
    {
        return 'https://t.trackedlink.net';
    }

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
        return 'dotdigital Engagement Cloud';
    }

    public function uninstall()
    {
        $this->postToDotmailer('uninstall');
        
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

        $this->registerController('backend', 'dotmailer');

        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopStartup',
            'onStartDispatch'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_Index',
            'extendTemplate'
        );

        $this->updateSchema();
        $this->createPluginID();
        $this->addMenuItem();

        return array('success' => true, 'invalidateCache' => array('frontend', 'backend'));
    }

    public function extendTemplate(Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        if ($view->hasTemplate()) {
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->extendsTemplate('backend/dotmailer_email_marketing/menuitem.tpl');
        }
    }

    public function createPluginID()
    {
        $plugin_id = null;
        $em = $this->Application()->Models();
        $settings = $em->find('Shopware\CustomModels\DotmailerEmailMarketing\DotmailerEmailMarketing', 1);
        
        if ($settings !== null) {
            $plugin_id = $settings->getPluginID();
        }

        if ($plugin_id === null) {
            $dotmailer_email_marketing = new DotmailerEmailMarketing();

            $length = 128;
            $crypto_strong = true;
            $dotmailer_email_marketing->setPluginID(bin2hex(openssl_random_pseudo_bytes($length, $crypto_strong)));

            $em->persist($dotmailer_email_marketing);
            $em->flush();
        }
    }

    public function getPluginID()
    {
	    require_once __DIR__ . '/Models/dotmailerEmailMarketing/dotmailerEmailMarketing.php';

        $em = $this->Application()->Models();
        $settings = $em->find('Shopware\CustomModels\DotmailerEmailMarketing\DotmailerEmailMarketing', 1);
        
        return $settings !== null ? $settings->getPluginID() : die();
    }

    public function addMenuItem()
    {
        $this->createMenuItem(
            array(
            'label' => 'dotdigital Engagement Cloud',
            'onclick' => 'window.open("/backend/dotmailer/connect", "_blank")',
            'class' => 'sprite-dotmailer-email-marketing',
            'active' => 1,
            'parent' => $this->Menu()->findOneBy(array('label' => 'Marketing'))
            )
        );
    }

    /**
     * Enable plugin method
     *
     * @return bool
     */
    public function enable()
    {
        $this->postToDotmailer('enable');
        
        return true;
    }

    /**
     * Disable plugin method
     *
     * @return bool
     */
    public function disable()
    {
        $this->postToDotmailer('disable');
        
        return true;
    }

    public function postToDotmailer($action)
    {
        $url = self::getTrackingSiteUrl() .  '/e/shopware/' . $action;
        $data = array(
            'pluginid' => $this->getPluginID()
        );

        $post = curl_init($url);

        curl_setopt($post, CURLOPT_POST, 1);
        curl_setopt($post, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_exec($post);
        curl_close($post);
    }

    /**
     * Creates the database scheme from an existing doctrine model.
     *
     * Will remove the table first, so handle with care.
     */
    protected function updateSchema()
    {
	    require_once __DIR__ . '/Models/dotmailerEmailMarketing/dotmailerEmailMarketing.php';

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
