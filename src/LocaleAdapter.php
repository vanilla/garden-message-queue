<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

use Gdn_Locale;
use Kaecyra\AppCommon\ConfigInterface;

/**
 * Vanilla locale adapter
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
class LocaleAdapter implements ConfigInterface
{
    /**
     * The adapted Vanilla locales
     *
     * @var Gdn_Locale
     */
    private $locale;

    /**
     *
     * @param Gdn_Locale $locale
     */
    public function __construct(Gdn_Locale $locale) {
        $this->locale = $locale;
    }

    /**
     * Get a locale setting
     *
     * @param string $setting
     * @param mixed $default
     * @return mixed
     */
    public function get($setting, $default = null) {
        return $this->locale->translate($setting, $default);
    }

    /**
     * Set a locale setting
     *
     * @param string $setting
     * @param mixed $value
     */
    public function set($setting, $value) {
        $this->locale->setTranslation($setting, $value);
    }

    /**
     * Delete a key from the locale
     *
     * @param string $setting
     */
    public function remove($setting): bool {
        $this->locale->setTranslation($setting, null);
        return true;
    }

    /**
     * Dump all settings from locales
     *
     * @return array
     */
    public function dump(): array {
        return $this->locale->getDefinitions();
    }

    /**
     * Save changes
     *
     * @param bool $force
     */
    public function save($force = false) {
        // no-op
    }

}
