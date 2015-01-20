<?php

/**
 * Database job queue
 *
 * @package   Slick\JobQueue\Queue
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Slick\JobQueue\Queue;

use DateTime;
use DateTimeZone;
use Slick\Common\Base;
use Slick\Database\Sql;
use Slick\JobQueue\Job\Status;
use Slick\JobQueue\Model\Task;
use Slick\JobQueue\Job\JobInterface;
use Slick\JobQueue\JobQueueInterface;
use Slick\Database\Adapter\AdapterInterface;

/**
 * Database job queue
 *
 * @package   Slick\JobQueue\Queue
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property AdapterInterface $adapter
 * @property string $tableName
 * @property DateTime $queryDate
 */
class Database extends Base implements JobQueueInterface
{
    /**
     * @var string Mysql date time format
     */
    const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @readwrite
     * @var AdapterInterface
     */
    protected $_adapter;

    /**
     * @readwrite
     * @var string
     */
    protected $_tableName = 'tasks';

    /**
     * @var array
     */
    protected static $_namespaces = [
        '\Slick\JobQueue\Job'
    ];

    /**
     * Gets query date for next job retrieve
     *
     * @return DateTime
     */
    public function getQueryDate()
    {
        return new DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * Returns next job in the queue
     *
     * If there is no more jobs to do, null will be returned
     *
     * @return JobInterface|null
     */
    public function next()
    {
        $record = Sql::createSql($this->adapter)
            ->select($this->tableName)
            ->where(
                [
                    'notBefore <= :date AND
                    status in (:queued, :postponed)' => [
                        ':date' => $this->getQueryDate()
                            ->format(self::MYSQL_DATE_FORMAT),
                        ':queued' => Status::Queued,
                        ':postponed' => Status::Postponed
                    ]
                ]
            )
            ->order('notBefore ASC')
            ->first();
        if (is_null($record)) {
            return null;
        }

        $reflection = new \ReflectionClass(
            $this->getClass($record['type'])
        );
        /** @var Task $job */
        $job = $reflection->newInstanceArgs([$record]);
        $job->fetched = $this->getQueryDate()->format(self::MYSQL_DATE_FORMAT);
        $job->status = Status::Ongoing;
        $this->save($job);
        return $job;
    }

    /**
     * Adds a job to the queue
     * @param JobInterface|Task $job
     * @return JobQueueInterface
     */
    public function add(JobInterface $job)
    {
        $job->status = Status::Queued;
        if (is_null($job->notBefore)) {
            $job->notBefore = $this->getQueryDate()->format(self::MYSQL_DATE_FORMAT);
        }
        $this->save($job);
    }

    /**
     * Places a job after it been executed
     *
     * @param JobInterface|Task $job
     * @return JobQueueInterface
     */
    public function finish(JobInterface $job)
    {
        return $this->save($job);
    }

    /**
     * Saves current job into DB
     *
     * @param Task $job
     * @return self
     */
    public function save(Task $job)
    {

        if (is_null($job->id)) {
            $sql = Sql::createSql($this->adapter)
                ->insert($this->tableName);
        } else {
            $sql = Sql::createSql($this->adapter)
                ->update($this->tableName)
                ->where(['id = :id' => [':id' => $job->id]]);
        }
        $sql->set($job->asArray())
            ->execute();
        return $this;
    }

    /**
     * Adds a namespace where to look for job classes
     * @param string $namespace
     */
    public static function addNamespace($namespace)
    {
        if (!in_array($namespace, static::$_namespaces)) {
            array_unshift(static::$_namespaces, $namespace);
        }
    }

    /**
     * Checks and returns the job class name for the given name/alias
     *
     * If an alias is given it will check the class name on all namespaces
     * present on Database::$_namespaces property
     *
     * @param string $alias
     * @return string
     *
     * @throws \UnexpectedValueException If there are no classes for the given
     *  name or alias.
     */
    public function getClass($alias)
    {
        if ($this->_checkClass($alias)) {
            return $alias;
        }

        foreach (static::$_namespaces as $namespace) {
            $className = $namespace.'\\'.$alias;
            if ($this->_checkClass($className)) {
                return $className;
            }
        }

        throw new \UnexpectedValueException(
            "Class '{$alias}' does not exists."
        );

    }

    /**
     * Check if a given class name exists and implements the correct
     * Job interface
     *
     * @param string $className
     * @return bool
     *
     * @throws \InvalidArgumentException If the class exists and it does not
     *  implements the <<\Slick\JobQueue\Job\JobInterface>> interface.
     */
    protected function _checkClass($className)
    {
        if (class_exists($className)) {
            $reflection = new \ReflectionClass($className);
            $ifc = '\Slick\JobQueue\Job\JobInterface';
            if (! $reflection->implementsInterface($ifc)) {
                throw new \InvalidArgumentException(
                    "Class '{$className}' does not implements <<{$ifc}>> interface."
                );
            }
            return true;
        }
        return false;
    }
}