<?php

/**
 * Model os a task
 *
 * @package   Slick\JobQueue\Model
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue\Model;

use DateTime;
use Slick\Common\Base;
use Slick\JobQueue\Worker;
use Slick\Common\Inspector;
use Slick\JobQueue\Job\Status;


/**
 * Model os a task
 *
 * @package   Slick\JobQueue\Model
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property int $id
 * @property string $type
 * @property mixed $data
 * @property DateTime $created
 * @property DateTime $notBefore
 * @property DateTime $fetched
 * @property Status $status
 * @property string $failureMessage
 * @property Worker $worker
 */
class Task extends Base
{

    /**
     * @readwrite
     * @var int
     */
    protected $_id;

    /**
     * @readwrite
     * @column
     * @var string
     */
    protected $_type;

    /**
     * @readwrite
     * @column
     * @var mixed
     */
    protected $_data;

    /**
     * @readwrite
     * @column
     * @var DateTime
     */
    protected $_created;

    /**
     * @readwrite
     * @column
     * @var DateTime
     */
    protected $_notBefore;

    /**
     * @readwrite
     * @column
     * @var DateTime
     */
    protected $_fetched;

    /**
     * @readwrite
     * @column
     * @var Status
     */
    protected $_status;

    /**
     * @readwrite
     * @column
     * @var string
     */
    protected $_failureMessage;

    /**
     * @readwrite
     * @var Worker
     */
    protected $_worker;

    /**
     * Exports the columns to an array
     *
     * @return array
     */
    public function asArray()
    {
        $inspector = new Inspector($this);

        $data = [];
        foreach ($inspector->getClassProperties() as $property) {
            $annotations = $inspector->getPropertyAnnotations($property);
            if ($annotations->hasAnnotation('@column')) {
                $name = trim($property, '_');
                $data[$name] = $this->$name;
            }
        }
        return $data;
    }

    /**
     * Retrieve the job queue of the worker that initiates this task
     *
     * @return \Slick\JobQueue\JobQueueInterface
     */
    public function getJobQueue()
    {
        return $this->_worker->jobQueue;
    }
}