<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

use Exception;
use Garden\Container\Container;
use Garden\Container\Reference;
use Garden\EventManager;
use Garden\QueueInterop\SchedulerAwareInterface;
use Garden\QueueInterop\SchedulerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Job queue
 *
 * The main service that accepts jobs and process them all by
 * delegating to an underlying Job Driver.
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
class JobQueue implements SchedulerInterface {

    /**
     * Queued jobs
     *
     * @var Job[]
     */
    private $jobs = [];

    /**
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     *
     * @var JobDriverInterface
     */
    private $driver = null;

    /**
     *
     * @var EventManager
     */
    private $eventManager = null;

    /**
     * Initialize an instance of the JobQueue
     *
     * @param EventManager $eventManager
     * @param JobDriverInterface $driver
     * @param LoggerInterface $logger
     */
    public function __construct(EventManager $eventManager, JobDriverInterface $driver, LoggerInterface $logger, $config = []) {
        $this->eventManager = $eventManager;
        $this->driver = $driver;
        $this->logger = $logger;

        // Hook to process all jobs at the end of the request
        $this->eventManager->bind('gdn_dispatcher_afterdispatch', function() use ($config) {

            if (!($config['DisableFastCgiFinishRequest'] ?? false)) {
                // Finish Flushes all response data to the client
                // so that job payloads can run without affecting the browser experience
                fastcgi_finish_request();
            }

            $this->processAll();
        });
    }

    /**
     * Configure itself in the dependency injection container.
     *
     * @param Container $di
     */
    public static function bootstrap(Container $di) {
        $di->removeAlias(LoggerInterface::class);

        $di
                // JobQueue
                ->rule('JobQueue')
                ->setClass(JobQueue::class)
                ->setConstructorArgs([
                    new Reference(['JobDriver']),
                    new Reference(['Gdn_Configuration', 'Garden.MessageQueue'])
                ])
                ->setShared(true)

                // JobDriver
                ->rule('JobDriver')
                ->setFactory(function() use ($di) {
                    JobDriver::bootstrap($di);
                    return new JobDriver($di, $di->get(LoggerInterface::class));
                })
                ->setShared(true)

                // Scheduler Aware
                ->rule(SchedulerAwareInterface::class)
                ->addCall('setScheduler')

                // Scheduler service (alias of the JobQueue)
                ->rule(SchedulerInterface::class)
                ->setAliasOf('JobQueue')

                // Default NULL logger
                ->rule(LoggerInterface::class)
                ->setClass(NullLogger::class)
        ;
    }

    /**
     * Add a new job to be processed asynchronously
     *
     * @param string $type
     * @param array $args
     * @return JobInterface
     */
    public function addJob($type, $args = []) : JobInterface {
        $this->logger->notice('Job is being added to the queue:  ' . $type . ' - ' .  serialize($args));

        $job = $this->driver->addJob($type, $args);
        $this->jobs[] = $job;
        return new JobReadOnlyProxy($job);
    }

    /**
     * Process all jobs
     *
     * Delegate all queued jobs to the driver for processing.
     */
    protected function processAll() {
        foreach ($this->jobs as &$job) {
            try {
                $this->driver->execute($job);
            } catch (Exception $ex) {
                $this->logger->error("Job failed to execute, exception received: " . $ex->getMessage());
            }
        }
    }

}
