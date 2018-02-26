<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

use Garden\QueueInterop\JobBridgeInterface;
use Garden\QueueInterop\JobContextInterface;
use Garden\QueueInterop\RunnableJobInterface;


/**
 * Abstract job.
 *
 * Provides a basic implementation for a job payload.
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
abstract class AbstractJob implements RunnableJobInterface {

    /**
     *
     * @var JobBridgeInterface
     */
    private $implementation;

    final public function __construct(JobBridgeInterface $implementation) {
        $this->implementation = $implementation;
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
     * Get an item in the job data
     *
     * @param string $name
     * @return mixed
     */
    public function get($name) {
        return $this->implementation->getJobContext()->get($name);
    }


    /**
     * Get job data
     *
     * @return array
     */
    public function getData(): array {
        return $this->implementation->getJobContext()->getData();
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
        // no-op
    }

    /**
     * Teardown method
     *
     * Called after the 'run' method
     */
    public function teardown() {
        // no-op
    }

}
