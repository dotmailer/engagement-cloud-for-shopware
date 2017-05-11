<?php


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
class Shopware_Plugins_Frontend_dotmailerEmailMarketing_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public function getVersion() {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'plugin.json'), true);
        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getLabel()
    {
        return 'dotmailerEmailMarketing';
    }

    public function uninstall()
    {
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('Shopware\CustomModels\dotmailerEmailMarketing\dotmailerEmailMarketing')
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

    	$this->createMenuItem(array(
        	'label' => 'dotmailer Email Marketing',
        	'onclick' => 'window.open("https://my.dotmailer.com", "_blank");',
        	'class' => 'sprite-star',
        	'active' => 1,
        	'parent' => $this->Menu()->findOneBy(['label' => 'Marketing'])
        ));

        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopStartup',
            'onStartDispatch'
        );

        $this->updateSchema();
        return array('success' => true, 'invalidateCache' => array('frontend', 'backend'));
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
            $em->getClassMetadata('Shopware\CustomModels\dotmailerEmailMarketing\dotmailerEmailMarketing')
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
        $this->registerCustomModels();        $this->registerMyTemplateDir();
        $this->registerMySnippets();


        $subscribers = array(
            new \Shopware\dotmailerEmailMarketing\Subscriber\ControllerPath(),
            new \Shopware\dotmailerEmailMarketing\Subscriber\Frontend()
        );

        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }
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
            'Shopware\dotmailerEmailMarketing',
            $this->Path()
        );
    }
}
