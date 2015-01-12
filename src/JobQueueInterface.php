<?php

/**
 * Job queue interface
 *
 * @package   Slick\JobQueue
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue;

use Slick\JobQueue\Job\JobInterface;

/**
 * This interface is used by worker to retrieve the jobs he has to do
 *
 * @package   Slick\JobQueue
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
interface JobQueueInterface
{

    /**
     * Returns next job in the queue
     * @return JobInterface
     */
    public function next();

    /**
     * Adds a job to the queue
     * @param JobInterface $job
     * @return JobQueueInterface
     */
    public function add(JobInterface $job);

    /**
     * Places a job after it been executed
     *
     * @param JobInterface $job
     * @return JobQueueInterface
     */
    public function finish(JobInterface $job);
}