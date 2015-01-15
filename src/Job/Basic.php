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

use DateTime;
use DateTimeZone;
use Slick\JobQueue\Model\Task;
use Slick\JobQueue\Queue\Database;

/**
 * Basic job
 *
 * @package   Slick\JobQueue\Job
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
class Basic extends Task implements JobInterface
{

    /**
     * @readwrite
     * @column
     * @var string
     */
    protected $_type = 'Basic';

    /**
     * @readwrite
     * @column
     * @var string
     */
    protected $_created;

    /**
     * Sets creation date
     * @param array $options
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
        $date = new DateTime('now', new DateTimeZone('UTC'));
        $this->created = $date->format(Database::MYSQL_DATE_FORMAT);
    }

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