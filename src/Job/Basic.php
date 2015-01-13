<?php

/**
 * Basic job
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue\Job;

use Slick\JobQueue\Model\Task;

/**
 * Basic job
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
class Basic extends Task implements JobInterface
{

    /**
     * Executes a job and returns its status
     *
     * @return Status
     */
    public function execute()
    {
        return Status::Done;
    }
}