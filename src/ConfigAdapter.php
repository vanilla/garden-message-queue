<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

use Gdn_Configuration;
use Kaecyra\AppCommon\ConfigInterface;

/**
 * Config adapter
 *
 * Vanilla configuration adapter
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
class ConfigAdapter implements ConfigInterface
{
    /**
     * The adapted Vanilla configuration
     *
     * @var Gdn_Configuration
     */
    private $config;

    /**
     *
     * @param Gdn_Configuration $config
     */
    public function __construct(Gdn_Configuration $config) {
        $this->config = $config;
        $this->config->autoSave(false);
    }

    /**
     * Get a config setting
     *
     * @param string $setting
     * @param mixed $default
     * @return mixed
     */
    public function get($setting, $default = null) {
        return $this->config->get($setting, $default);
    }

    /**
     * Set a config setting
     *
     * @param string $setting
     * @param mixed $value
     */
    public function set($setting, $value) {
        $this->config->set($setting, $value, true, true);
    }

    /**
     * Delete a key from the config
     *
     * @param string $setting
     */
    public function remove($setting): bool {
        return $this->config->remove($setting);
    }

    /**
     * Dump all settings from config
     *
     * @return array
     */
    public function dump(): array {
        return $this->config->Data;
    }

    /**
     * Save changes
     *
     * @param bool $force
     */
    public function save($force = false) {
        $this->config->save();
    }

}
