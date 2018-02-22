<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license GPLv2
 */

namespace Garden\MessageQueue;

use Garden\QueueInterop\ExecutableJobInterface;
use Garden\QueueInterop\JobContextInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;


/**
 * Abstract job.
 *
 * Provides a basic implementation for a job payload.
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 * @version 1.0
 */
abstract class AbstractJob implements ExecutableJobInterface {

    /**
     *
     * @var JobContextInterface
     */
    private $jobContext;

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    final public function __construct(JobContextInterface $jobContext, LoggerInterface $logger = null) {
        $this->jobContext = $jobContext;
        $this->logger = $logger;

        // Provides a Null logger if none has been set
        if (is_null($this->logger)) {
            $this->logger = new NullLogger();
        }
    }

    /**
     * Get the Job Context object
     *
     * @return JobContextInterface
     */
    public function getJobContext() {
        return $this->jobContext;
    }

    /**
     * Get an item in the job data
     *
     * @param string $name
     * @return mixed
     */
    public function get($name) {
        return $this->jobContext->get($name);
    }


    /**
     * Get job data
     *
     * @return array
     */
    public function getData(): array {
        return $this->jobContext->getData();
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

        $this->logger->log($level, $message, $context);
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
