<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

use Garden\QueueInterop\Job\JobInterface;
use Garden\QueueInterop\Job\JobStatus;
use Garden\QueueInterop\JobBridgeInterface;
use Garden\QueueInterop\JobContextInterface;

/**
 * Abstract job.
 *
 * Provides a basic implementation for a job payload.
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
abstract class AbstractJob implements JobInterface {

    /**
     *
     * @var JobBridgeInterface
     */
    private $implementation;

    /**
     * Job start time
     * @var float
     */
    protected $startTime;

    /**
     * Job duration
     * @var float
     */
    protected $duration;

    final public function __construct(JobBridgeInterface $implementation) {
        $this->implementation = $implementation;
        $this->setStatus(JobStatus::RECEIVED);
        $this->startTime = microtime(true);
    }

    /**
     * Get the Job Context object
     *
     * @return JobContextInterface
     */
    public function getJobContext() {
        return $this->implementation->getJobContext();
    }

    /**
     * Get job ID
     *
     * @return string
     */
    public function getID(): string {
        return $this->getJobContext()->getID();
    }

    /**
     * Set job id
     *
     * @param string $id
     */
    public function setID(string $id) {
        $this->getJobContext()->setID($id);
    }

    /**
     * Get the job name
     *
     * @return string
     */
    public function getName(): string {
        return static::class;
    }

    /**
     * Get an item in the job data
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null) {
        return $this->getJobContext()->get($name, $default);
    }

    /**
     * Get job data
     *
     * @return array
     */
    public function getData(): array {
        return $this->getJobContext()->getData();
    }

    /**
     * Set job data
     *
     * @param array $data
     */
    public function setData(array $data) {
        $this->getJobContext()->setData($data);
    }

    /**
     * Get execution time
     *
     * @return float
     */
    public function getDuration(): float {
        return $this->duration ? $this->duration : microtime(true) - $this->startTime;
    }

    /**
     * Get job status
     *
     * @return string
     */
    public function getStatus(): string {
        return $this->getJobContext()->getStatus();
    }

    /**
     * Set job status
     *
     * @param string $status
     */
    public function setStatus(string $status) {
        $this->getJobContext()->setStatus($status);
    }

    /**
     * Output to log (screen or file or both)
     *
     * @param string $level logger event level
     * @param string $message
     * @param array $context optional.
     * @param type $options optional.
     */
    protected function log(string $level, string $message, array $context = []) {
        if (!is_array($context)) {
            $context = [];
        }

        $context = array_merge([
            'pid' => posix_getpid(),
            'time' => date('Y-m-d H:i:s'),
            'message' => $message
        ], $context);

        $this->implementation->getLogger()->log($level, $message, $context);
    }

    /**
     * Setup method
     *
     * Called before the 'run' method
     */
    public function setup() {
        $this->setStatus(JobStatus::INPROGRESS);
    }

    /**
     * Teardown method
     *
     * Called after the 'run' method
     */
    public function teardown() {
        $this->setStatus(JobStatus::COMPLETE);
        $this->duration = microtime(true) - $this->startTime;
    }

}
