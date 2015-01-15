<?php

/**
 * Created by PhpStorm.
 * User: fsilva
 * Date: 15-01-2015
 * Time: 18:30
 */

namespace Slick\JobQueue\Logger\Formatter;

use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;

class ConsoleFormatter implements FormatterInterface
{

    /**
     * Translates Monolog log levels to Wildfire levels.
     */
    private $logLevels = array(
        Logger::DEBUG => '<comment>Debug:</comment> ',
        Logger::INFO => '<info>Info:</info> ',
        Logger::NOTICE => '<info>Notice:</info> ',
        Logger::WARNING => '<comment>Warning:</comment> ',
        Logger::ERROR => '<error>Error:</error> ',
        Logger::CRITICAL => '<error>Critical:</error> ',
        Logger::ALERT => '<error>Alert:</error> ',
        Logger::EMERGENCY => '<error>Emergency:</error> ',
    );

    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $message = $this->logLevels[$record['level']];
        $message .= $record['message'];
        $record['formatted'] = $message;
        return $record;
    }

    /**
     * Formats a set of log records.
     *
     * @param  array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        foreach ($records as $key => $value) {
            $records[$key] = $this->format($value);
        }
        return $records;
    }
}