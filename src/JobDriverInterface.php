<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

/**
 * Job driver interface
 *
 * The interface a job driver must implement to accept and process jobs
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
interface JobDriverInterface {

    /**
     * Add a new job to be processed asynchronously
     *
     * @param string $type
     * @param array $args
     * @return JobInterface The scheduled job
     */
    public function addJob($type, $args) : JobInterface;

    /**
     * Execute a job
     *
     * @param Job $job
     */
    public function execute(Job $job);

}
