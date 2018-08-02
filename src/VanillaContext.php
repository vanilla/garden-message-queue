<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

use Garden\QueueInterop\ConfigInterface;
use Garden\QueueInterop\VanillaContextInterface;

/**
 * Vanilla context
 *
 * Vanilla customer context implementation
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
class VanillaContext implements VanillaContextInterface {

    /**
     * Config
     *
     * @var ConfigInterface
     */
    private $config;

    /**
     * Locale
     *
     * @var ConfigInterface
     */
    private $locale;

    /**
     *
     * @param ConfigAdapter $config
     * @param LocaleAdapter $locale
     */
    public function __construct(ConfigAdapter $config, LocaleAdapter $locale) {
        $this->config = $config;
        $this->locale = $locale;
    }

    /**
     * Get site config
     *
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface {
        return $this->config;
    }

    /**
     * Get site locale
     *
     * @return ConfigInterface
     */
    public function getLocale(): ConfigInterface {
        return $this->locale;
    }

}
