<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

/**
 * Read-only job interface
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
interface JobInterface {

    /**
     * Get the job ID
     *
     * @return string The job ID
     */
    public function getID();

    /**
     * Get the job status
     *
     * @return string The job status
     */
    public function getStatus();

}
