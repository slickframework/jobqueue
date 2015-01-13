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
use Slick\JobQueue\Job\Basic;
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
     * Starts this worker
     * @return self
     */
    public function run()
    {
        $start = time();
        $exit = false;
        while (!$exit) {
            /** @var Basic $job */
            $job = $this->_jobQueue->next();
            if ($job) {
                $job->status = $job->execute();
                $this->_jobsExecuted++;
                $this->_jobQueue->finish($job);
            } else {
                if ($this->_exitWhenNothingToDo) {
                    $exit = true;
                }
            }

            $now = time() - $start;
            if ($this->_timeout <= $now) {
                $exit = true;
            } else {
                sleep($this->_sleepTime);
            }
        }

    }
}