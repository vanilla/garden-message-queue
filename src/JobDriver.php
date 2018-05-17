<?php

/**
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\MessageQueue;

use Exception;
use Garden\Container\Container;
use Garden\Container\Reference;
use Garden\Db\Db;
use Garden\Db\Drivers\MySqlDb;
use Garden\MessageQueue\Bridge\JobBridge;
use Garden\MessageQueue\VanillaContext;
use Garden\QueueInterop\DatabaseAwareInterface;
use Garden\QueueInterop\Job\JobStatus;
use Garden\QueueInterop\JobBridgeInterface;
use Garden\QueueInterop\JobContext;
use Garden\QueueInterop\JobContextAwareInterface;
use Garden\QueueInterop\JobContextInterface;
use Garden\QueueInterop\RunnableJobInterface;
use Garden\QueueInterop\VanillaContextAwareInterface;
use Garden\QueueInterop\VanillaContextInterface;
use PDO;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Job driver
 *
 * A driver that accepts job and process them locally on the webserver.
 *
 * @author Eric Vachaviolos <eric.v@vanillaforums.com>
 * @package garden-message-queue
 */
class JobDriver implements JobDriverInterface {

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Dependency injection container
     *
     * @var Container
     */
    private $di;

    /**
     * Initialize an instance of the JobDriver
     *
     * @param Container $di
     * @param LoggerInterface $logger
     */
    public function __construct(Container $di, LoggerInterface $logger) {
        $this->di = $di;
        $this->logger = $logger;

    }

    /**
     * Configure itself in the dependency injection container.
     *
     * @param Container $di
     */
    public static function bootstrap(Container $di) {
        // Provides the implementation for the AbstractJob class
        $di
                ->rule(JobBridgeInterface::class)
                ->setClass(JobBridge::class)
        ;

        // Provides the PDO connection
        $di
                ->rule(PDO::class)
                ->setFactory(function() {
                    $dbName = C('Database.Name');
                    $dbHost = C('Database.Host');
                    $dbUser = C('Database.User');
                    $dbPass = C('Database.Password');
                    $charset = C('Database.CharacterEncoding') ?? 'utf8mb4';

                    $dsn = "mysql:dbname={$dbName};charset={$charset};host={$dbHost}";

                    $options = [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false
                    ];

                    return new PDO($dsn, $dbUser, $dbPass, $options);

                })
                ->setShared(true)
        ;

        // Configures the setters for all "Aware" interfaces
        // and provide their corresponding injected implementations
        $di
                // Logger Aware
                ->rule(LoggerAwareInterface::class)
                ->setShared(true)
                ->addCall('setLogger')

                // Dataware Aware
                ->rule(DatabaseAwareInterface::class)
                ->addCall('setDatabase')

                // Database Implementation
                ->rule(Db::class)
                ->setFactory(function (PDO $pdo) {
                    return new MySqlDb($pdo, 'GDN_');
                })
                ->setShared(true)

                // JobContext Aware
                ->rule(JobContextAwareInterface::class)
                ->addCall('setJobContext', [new Reference(JobContextInterface::class)])

                // JobContext Implementation
                ->rule(JobContextInterface::class)
                ->setClass(JobContext::class)
                ->setShared(true)

                // VanillaContext Aware
                ->rule(VanillaContextAwareInterface::class)
                ->addCall('setVanillaContext')

                // VanillaContext Implementation
                ->rule(VanillaContextInterface::class)
                ->setClass(VanillaContext::class)
        ;
    }

    /**
     * Add a new job to be processed asynchronously
     *
     * @param string $type
     * @param array $args
     * @return JobInterface The scheduled job
     */
    public function addJob($type, $args): JobInterface {
        $job = new Job($type, $args);

        // Assign a unique job id right away
        $id = uniqid('localjob', true);
        $job->setID($id);
        $job->setStatus(JobStatus::RECEIVED);

        $this->logger->notice('Job has been scheduled: ' . $job->getType() . ' - ' .  serialize($job->getArgs()));

        return $job;
    }

    /**
     * Execute a job
     *
     * This will execute the job payload locally on the webserver
     *
     * @param Job $job
     */
    public function execute(Job $job) {
        $type = $job->getType();
        $args = $job->getArgs();

        if (!class_exists($type)) {
            $job->setStatus(JobStatus::ERROR);
            throw new Exception(sprintf("The job class '%s' cannot be found", $type));
        }

        // Prepare a job context for the current payload
        $jobContext = $this->di->get(JobContextInterface::class); /* @var $jobContext JobContextInterface */
        $jobContext->setData($args);

        // Resolve the job payload
        $jobPayload = $this->di->get($type);

        if (! $jobPayload instanceof RunnableJobInterface) {
            $job->setStatus(JobStatus::ERROR);
            throw new Exception(sprintf("The job class '%s' does not implement the RunnableJobInterface", $type));
        }

        $this->logger->notice('Job is executing: ' . $type . ' - ' .  serialize($args));

        // Setup job
        $jobPayload->setup();

        // Run the job payload
        $jobPayload->run();

        // Teardown job
        $jobPayload->teardown();

        // Destroy the job context after the payload was executed
        $this->di->setInstance(JobContextInterface::class, null);
    }

}
