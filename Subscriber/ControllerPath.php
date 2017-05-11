<?php

namespace Shopware\dotmailerEmailMarketing\Subscriber;

use Enlight\Event\SubscriberInterface;

class ControllerPath implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
                        'Enlight_Controller_Dispatcher_ControllerPath_Backend_dotmailerEmailMarketing' => 'onGetControllerPathBackend',        );
    }


    /**
     * Register the backend controller
     *
     * @param         \Enlight_Event_EventArgs $args
     * @return        string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_dotmailerEmailMarketing     
*/
    public function onGetControllerPathBackend(\Enlight_Event_EventArgs $args)
    {
        return __DIR__ . '/../Controllers/Backend/dotmailerEmailMarketing.php';
    }


}
