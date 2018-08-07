# Garden Message Queue

This component provides everything needed for Vanilla to schedule jobs and process them.

It provides two main services, a `JobQueue` and a `JobDriver`.  The `JobQueue` provides a scheduling service while the `JobDriver` provides an implementation which executes job payloads on the web server itself.

## How to set it up in Vanilla?

- Add `vanilla/garden-message-queue` to the `require` section in  Vanilla's `composer.json`.
- Run `composer update`
- Bootstrap this component by adding the following snippet in Vanilla's `bootstrap.php`.

```
// Bootstrap the MessageQueue component
\Garden\MessageQueue\JobQueue::bootstrap(Gdn::getContainer());
```

Once configured, you can access the scheduler to add a job as followed:

```
/* @var $jobQueue \Garden\MessageQueue\JobQueue */
$jobQueue = Gdn::getContainer()->get('JobQueue');

// Schedules a job
$job = $jobQueue->addJob(EchoJob::class, ['message' => 'Hello World!']);
echo "EchoJob was scheduled with ID: " . $job->getID() . " and status: " . $job->getStatus();

```

At the end of the web request, once `GDN_Dispatcher` is done rendering the view, all jobs scheduled with the `JobQueue` will be processed one after another.  A call to [fastcgi\_finish\_request()](http://php.net/manual/en/function.fastcgi-finish-request.php) will be made so that all processing can run in the background without hanging the browser rendering.

## How to execute jobs on a dedicated queue system?

You can provide your own driver by implementing the `JobDriverInterface` interface and then configure the container to use that custom job driver instead of the default one.

```
// Switch implementation for the 'JobQueue' service
Gdn::getContainer()
        ->rule('JobQueue')
        ->setClass(ExternalJobQueue::class)
        ->setShared(true)
```

This new driver would then be responsible to delegate jobs to an external queue system such as RabbitMQ.  A new job processor would have to be implemented in order to consume those jobs.  Take a look at the [Queue Interop](https://github.com/vanilla/queue-interop) repo for hints as to what the implementation would entail.
