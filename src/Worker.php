<?php

/**
 * Generic worker
 *
 * @package   Slick\JobQueue
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue;

use Slick\Common\Base;
use Psr\Log\LoggerInterface;
use Slick\JobQueue\Job\Basic;
use Slick\JobQueue\Job\Status;
use Slick\JobQueue\Worker\WorkerInterface;

/**
 * Generic worker
 *
 * @package   Slick\JobQueue
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property JobQueueInterface $jobQueue
 * @property int $sleepTime
 * @property int $timeout
 * @property bool $exitWhenNothingToDo
 *
 * @property-read int $jobsExecuted
 */
class Worker extends Base implements WorkerInterface
{

    /**
     * @readwrite
     * @var JobQueueInterface
     */
    protected $_jobQueue;

    /**
     * @readwrite
     * @var int
     */
    protected $_sleepTime = 10;

    /**
     * @readwrite
     * @var int
     */
    protected $_timeout = 120;

    /**
     * @readwrite
     * @var bool
     */
    protected $_exitWhenNothingToDo = false;

    /**
     * @read
     * @var int
     */
    protected $_jobsExecuted = 0;

    /**
     * @readwrite
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @readwrite
     * @var LoggerInterface
     */
    protected $_externalLogger;

    /**
     * Starts this worker
     * @return self
     */
    public function run()
    {
        $start = time();
        $exit = false;
        $this->_logger->info("Staring worker...\n----------------------------");
        $this->info('Worker starts its work', $this->addLoggerContext([]));

        while (!$exit) {
            /** @var Basic $job */
            $job = $this->_jobQueue->next();
            if ($job) {
                try {
                    $jobStarts = microtime(true);
                    $job->worker = $this;
                    $this->_logger->info("Executing job...");
                    $job->status = $job->execute();
                    $this->_jobsExecuted++;
                    //$this->_jobQueue->finish($job);
                    $this->_logger->info("... done!\n");

                    $time_elapsed_secs = microtime(true) - $jobStarts;
                    $data = $job->asArray();
                    $this->info('Queued job was done!', $this->addLoggerContext(array_merge($data, ['time' => $time_elapsed_secs])));

                } catch (\Exception $caught) {
                    $job->failureMessage = $caught->getMessage();
                    $data = [
                        'exception' => get_class($caught),
                        'error_message' => $caught->getMessage(),
                        'file' => $caught->getFile().' line '.$caught->getLine(),
                        'trace' => $caught->getTraceAsString(),
                    ];
                    $this->error('Queued job failed', $this->addLoggerContext(array_merge($job->asArray(), $data)));
                    $job->status = Status::Fail;
                    $this->_logger->info("... fail!\n");
                } finally {
                    $this->_jobQueue->finish($job);
                }

            } else {
                if ($this->_exitWhenNothingToDo) {
                    $exit = true;
                }
            }

            $now = time() - $start;
            if ($this->_timeout <= $now) {
                $exit = true;
            } else {
                $this->_logger->info("No more jobs. Sleeping for {$this->_sleepTime} seconds\n");
                sleep($this->_sleepTime);
            }
        }
        $this->_logger->info("----------------------------\nI finish my work. {$this->jobsExecuted} job(s) executed.\n");
        $data = ['jobs' => $this->jobsExecuted];
        $this->info('Worker finished its work', $this->addLoggerContext($data));

        return $this;
    }

    private function error($msg, $data)
    {
        if (! $this->_externalLogger) return;

        $this->_externalLogger->error($msg, $data);
    }

    private function info($msg, $data)
    {
        if (! $this->_externalLogger) return;

        $this->_externalLogger->info($msg, $data);
    }

    private function addLoggerContext(array $data)
    {
        $context = [
            'host' => $_SERVER['SERVER_NAME'],
            'server_address' => $_SERVER['SERVER_ADDR'],
            'agent' => 'Queued job'
        ];
        return array_merge($context, $data);
    }
}