<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue\Bridge;

use Garden\QueueInterop\JobBridgeInterface;
use Garden\QueueInterop\JobContextInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Job bridge
 *
 * Provides an implementation to the AbstractJob.
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
class JobBridge implements JobBridgeInterface {

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

    public function __construct(JobContextInterface $jobContext, LoggerInterface $logger = null) {
        $this->jobContext = $jobContext;
        $this->logger = $logger;

        // Provides a Null logger if none has been set
        if (is_null($this->logger)) {
            $this->logger = new NullLogger();
        }
    }

    /**
     * Get the JobContext interface
     *
     * @return JobContextInterface
     */
    public function getJobContext(): JobContextInterface {
        return $this->jobContext;
    }

    /**
     * Get the Logger interface
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface {
        return $this->logger;
    }

}
