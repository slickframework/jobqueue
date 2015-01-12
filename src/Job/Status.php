<?php

/**
 * Job status
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue\Job;

use SplEnum;

/**
 * Job status
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
class Status extends SplEnum
{

    /**#@+
     * @var string Job states
     */
    const Queued    = 'queued';
    const Ongoing   = 'ongoing';
    const Postponed = 'postponed';
    const Done      = 'done';
    const Fail      = 'fail';
    /**#@- */

    const __default = self::Queued;
}