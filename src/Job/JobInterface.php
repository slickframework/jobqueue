<?php

/**
 * Job interface
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue\Job;

/**
 * Job interface
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
interface JobInterface
{

    /**
     * Executes a job and returns its status
     *
     * @return Status
     */
    public function execute();
}