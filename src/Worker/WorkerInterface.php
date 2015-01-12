<?php

/**
 * Worker interface
 *
 * @package   Slick\JobQueue\Worker
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue\Worker;

/**
 * Define worker the performs jobs (tasks)
 *
 * @package   Slick\JobQueue\Worker
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
interface WorkerInterface
{

    /**
     * Starts this worker
     * @return self
     */
    public function run();
}