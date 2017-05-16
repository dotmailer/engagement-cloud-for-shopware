<?php

namespace Shopware\CustomModels\DotmailerEmailMarketing;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="s_plugin_dotmailer_email_marketing")
 * @ORM\Entity(repositoryClass="Repository")
 */
class DotmailerEmailMarketing extends ModelEntity
{
    /**
     *
     * @var string $plugin_id
     *
     * @ORM\Id
     * @ORM\Column(name="plugin_id", type="string", nullable=false)
     */
    private $plugin_id;

    /**
     * return string
     */
    public function getPluginID()
    {
        return $this->plugin_id;
    }

    /**
     * @param $plugin_id string
     */
    public function setPluginID($plugin_id)
    {
        $this->plugin_id = $plugin_id;
    }
}
