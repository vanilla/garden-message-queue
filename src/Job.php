<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

/**
 * Job
 *
 * A class representing a job to be scheduled and processed
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
class Job implements JobInterface {

    /**
     * ID
     *
     * @var string
     */
    private $id;

    /**
     * Status
     *
     * @var string
     */
    private $status;

    /**
     * Class type
     *
     * @var string
     */
    private $type;

    /**
     * Arguments
     *
     * @var array
     */
    private $args;

    public function __construct($type, $args = []) {
        $this->type = $type;
        $this->args = $args;
    }

    /**
     * Get the job class type
     *
     * @return string The job class type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get the job arguments
     *
     * @return array The job arguments
     */
    public function getArgs() {
        return $this->args;
    }

    /**
     * Get the job ID
     *
     * @return string The job ID
     */
    public function getID() {
        return $this->id;
    }

    /**
     * Set the job ID
     *
     * @param string $id
     */
    public function setID($id) {
        $this->id = $id;
    }

    /**
     * Get the job status
     *
     * @return string The job status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set the job status
     *
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

}
