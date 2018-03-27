<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

/**
 * Job read-only proxy
 *
 * Provides a READ-ONLY proxy to an underlying job object.
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
class JobReadOnlyProxy implements JobInterface {

    /**
     * Underlying job object
     *
     * @var Job
     */
    private $job;

    public function __construct(JobInterface $job) {
        $this->job = $job;
    }

    /**
     * Get the job ID
     *
     * @return string The job ID
     */
    public function getID() {
        return $this->job->getID();
    }

    /**
     * Get the job status
     *
     * @return string The job status
     */
    public function getStatus() {
        return $this->job->getStatus();
    }

}
