<?php

/**
 * Database queue test case
 *
 * @package   Test\Queue
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 * @copyright 2015 SlickFramework
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @since     Version 1.0.0
 */

namespace Queue;

use Codeception\Util\Stub;
use Slick\Database\Sql\Dialect;
use Slick\Database\Sql\Select;
use Slick\JobQueue\Job\JobInterface;
use UnitTester;
use Slick\JobQueue\Job\Basic;
use Codeception\TestCase\Test;
use Slick\JobQueue\Queue\Database;

/**
 * Database queue test case
 *
 * @package   Test\Queue
 * @author    Filipe Silva <silvam.filipe@gmail.com>
 */
class DatabaseTest extends Test
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * Retrieve a valid JobInterface class name
     * @test
     * @expectedException \UnexpectedValueException
     *
     */
    public function getAValidJobClassName()
    {
        $this->tester->am('developer');
        $this->tester->amGoingTo('instantiate a job class trough Database queue object using alias "Basic"');
        $database = new Database();
        $class = $database->getClass('Basic');
        $this->tester->expectTo('get full class name like: \Slick\JobQueue\Job\Basic ');
        $this->assertEquals('\Slick\JobQueue\Job\Basic', $class);

        $class = $database->getClass('Slick\JobQueue\Job\Basic');
        $this->assertEquals('Slick\JobQueue\Job\Basic', $class);

        $database->getClass('foo');
    }

    /**
     * Get an invalid JobInterface class
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function getInvalidJobClassName()
    {
        $this->tester->am('developer');
        $this->tester->amGoingTo('instantiate a job class trough Database queue object using invalid class');
        $database = new Database();
        $this->tester->expectTo('get an InvalidArgumentException');
        $database->getClass('stdClass');
    }

    /**
     * Adding a namespace to database queue
     * @test
     */
    public function addANamespace()
    {
        $this->tester->am('developer');
        $this->tester->amGoingTo('add a namespace to Database queue');
        $this->tester->lookForwardTo('can use custom job class with alias');
        Database::addNamespace('Queue');
        $database = new Database();
        $class = $database->getClass('MyBasic');
        $this->tester->expectTo('get full class name like: Queue\MyBasic');
        $this->assertEquals('Queue\MyBasic', $class);

    }

    /**
     * Get the next job from database
     * @test
     */
    public function getNextTaskFromDatabase()
    {
        $this->tester->am('developer');
        $this->tester->amGoingTo('retrieve the next job from database queue');
        $database = new Database();
        $return = [
            0 => [
                [
                    'id' => 1092,
                    'type' => 'Basic'
                ],
            ],
            1 => []
        ];
        $adapter = Stub::makeEmpty('Slick\Database\Adapter\MysqlAdapter', [
            'getDialect' => function() {return Dialect::MYSQL;},
            'query' => function(Select $sql, $parameters = []) use ($return, $database) {
                static $count;
                if (!$count) {
                    $count = 0;
                }
                $this->assertEquals(
                    [
                        ':date' => $database->getQueryDate()->format('Y-m-d H:i:s')
                    ],
                    $parameters
                );
                $query = $sql->getQueryString();
                $expected = "SELECT tasks.* FROM tasks WHERE notBefore <= :date ORDER BY notBefore ASC LIMIT 1";
                $this->assertEquals($expected, $query);
                return ($return[$count++]);
            }
        ]);
        $database->adapter = $adapter;
        $job = $database->next();
        $this->tester->expectTo('get a JobInterface object');
        $this->assertTrue($job instanceof JobInterface);
        $this->tester->amGoingTo('retrieve the next job on database');
        $this->tester->expectTo('get a null stating that are no more jobs in queue');
        $this->assertNull($database->next());
    }

}

/**
 * Class MyBasic
 * @package Queue
 */
class MyBasic extends Basic
{

}