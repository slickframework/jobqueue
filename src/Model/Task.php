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
     * @var string
     */
    protected $_type;

    /**
     * @readwrite
     * @var mixed
     */
    protected $_data;

    /**
     * @readwrite
     * @var DateTime
     */
    protected $_created;

    /**
     * @readwrite
     * @var DateTime
     */
    protected $_notBefore;

    /**
     * @readwrite
     * @var DateTime
     */
    protected $_fetched;

    /**
     * @readwrite
     * @var Status
     */
    protected $_status;

    /**
     * @readwrite
     * @var string
     */
    protected $_failureMessage;

}