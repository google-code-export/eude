<?php

/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 * phpcron_runtime based on CronParser by Nikol S released on 12 Sep 2005 under GPL license
 */
abstract class phpcron_runtime {

    private $bits = Array(); //exploded String like 0 1 * * *
    private $now = Array(); //Array of cron-style entries for time()
    public $lastRan = 0;
    private $year = NULL;
    private $month = NULL;
    private $day = NULL;
    private $hour = NULL;
    private $minute = NULL;
    private $hours_arr = array();
    private $minutes_arr = array();
    private $months_arr = array();

    public function evaluate_job() {
        if ($lastRan != 0)
            return true;

        $string = preg_replace('/[\s]{2,}/', ' ', $this->CronPattern);

        if (preg_match('/[^-,* \\d]/', $string) !== 0) {
            trigger_error('Cron String contains invalid character');
            return false;
        }
        $this->bits = @explode(" ", $string);

        if (count($this->bits) != 5) {
            trigger_error('Cron string is invalid. Too many or too little sections after explode');
            return false;
        }

        //put the current time into an array
        $this->now = explode(',', strftime('%M,%H,%d,%m,%w,%Y', time()));

        $this->year = $this->now[5];

        // Test du mois
        $arMonths = $this->_getMonthsArray();
        do {
            $this->month = array_pop($arMonths);
        } while ($this->month > $this->now[3]);

        if ($this->month === NULL) {
            $this->year = $this->year - 1;
            $arMonths = $this->_getMonthsArray();
            $this->_prevMonth($arMonths);
        } elseif ($this->month == $this->now[3]) {
            $arDays = $this->_getDaysArray($this->month, $this->year);

            do {
                $this->day = array_pop($arDays);
            } while ($this->day > $this->now[2]);

            if ($this->day === NULL) {
                $this->_prevMonth($arMonths);
            } elseif ($this->day == $this->now[2]) {
                $arHours = $this->_getHoursArray();

                do {
                    $this->hour = array_pop($arHours);
                } while ($this->hour > $this->now[1]);

                if ($this->hour === NULL) {
                    $this->_prevDay($arDays, $arMonths);
                } elseif ($this->hour < $this->now[1]) {
                    $this->minute = $this->_getLastMinute();
                } else {
                    $arMinutes = $this->_getMinutesArray();
                    do {
                        $this->minute = array_pop($arMinutes);
                    } while ($this->minute > $this->now[0]);

                    if ($this->minute === NULL) {
                        $this->_prevHour($arHours, $arDays, $arMonths);
                    }
                }
            } else {
                $this->hour = $this->_getLastHour();
                $this->minute = $this->_getLastMinute();
            }
        } else {
            $this->day = $this->_getLastDay($this->month, $this->year);
            if ($this->day === NULL) {
                $this->_prevMonth($arMonths);
            } else {
                $this->hour = $this->_getLastHour();
                $this->minute = $this->_getLastMinute();
            }
        }

        //if the last due is beyond 1970
        if ($this->minute === NULL) {
            trigger_error('Error calculating last due time');
            return false;
        } else {
            $this->lastRan = mktime($this->hour, $this->minute, 0, $this->month, $this->day, $this->year);
            return true;
        }
    }

    //given a month/year, return an array containing all the days in that month
    private function getDays($month, $year) {
        $daysinmonth = $this->daysinmonth($month, $year);
        $days = array();
        for ($i = 1; $i <= $daysinmonth; $i++) {
            $days[] = $i;
        }
        return $days;
    }

    private function daysinmonth($month, $year) {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }

    private function _getLastMinute() {
        $minutes = $this->_getMinutesArray();
        $minute = array_pop($minutes);

        return $minute;
    }

    private function _getLastHour() {
        $hours = $this->_getHoursArray();
        $hour = array_pop($hours);

        return $hour;
    }

    private function _getLastDay($month, $year) {
        //put the available days for that month into an array
        $days = $this->_getDaysArray($month, $year);
        $day = array_pop($days);

        return $day;
    }

    private function _getMinutesArray() {
        if (empty($this->minutes_arr)) {
            $minutes = array();

            if ($this->bits[0] == '*') {
                for ($i = 0; $i <= 60; $i++) {
                    $minutes[] = $i;
                }
            } else {
                $minutes = $this->expand_ranges($this->bits[0]);
                $minutes = $this->_sanitize($minutes, 0, 59);
            }
            $this->minutes_arr = $minutes;
        }
        return $this->minutes_arr;
    }

    private function _getHoursArray() {
        if (empty($this->hours_arr)) {
            $hours = array();

            if ($this->bits[1] == '*') {
                for ($i = 0; $i <= 23; $i++) {
                    $hours[] = $i;
                }
            } else {
                $hours = $this->expand_ranges($this->bits[1]);
                $hours = $this->_sanitize($hours, 0, 23);
            }

            $this->hours_arr = $hours;
        }
        return $this->hours_arr;
    }

    //given a month/year, list all the days within that month fell into the week days list.
    private function _getDaysArray($month, $year = 0) {
        if ($year == 0) {
            $year = $this->year;
        }

        $days = array();

        //return everyday of the month if both bit[2] and bit[4] are '*'
        if ($this->bits[2] == '*' AND $this->bits[4] == '*') {
            $days = $this->getDays($month, $year);
        } else {
            //create an array for the weekdays
            if ($this->bits[4] == '*') {
                for ($i = 0; $i <= 6; $i++) {
                    $arWeekdays[] = $i;
                }
            } else {
                $arWeekdays = $this->expand_ranges($this->bits[4]);
                $arWeekdays = $this->_sanitize($arWeekdays, 0, 7);

                //map 7 to 0, both represents Sunday. Array is sorted already!
                if (in_array(7, $arWeekdays)) {
                    if (in_array(0, $arWeekdays)) {
                        array_pop($arWeekdays);
                    } else {
                        $tmp[] = 0;
                        array_pop($arWeekdays);
                        $arWeekdays = array_merge($tmp, $arWeekdays);
                    }
                }
            }

            if ($this->bits[2] == '*') {
                $daysmonth = $this->getDays($month, $year);
            } else {
                $daysmonth = $this->expand_ranges($this->bits[2]);
                // so that we do not end up with 31 of Feb
                $daysinmonth = $this->daysinmonth($month, $year);
                $daysmonth = $this->_sanitize($daysmonth, 1, $daysinmonth);
            }

            //Now match these days with weekdays
            foreach ($daysmonth AS $day) {
                $wkday = date('w', mktime(0, 0, 0, $month, $day, $year));
                if (in_array($wkday, $arWeekdays)) {
                    $days[] = $day;
                }
            }
        }
        return $days;
    }

    private function _getMonthsArray() {
        if (empty($this->months_arr)) {
            $months = array();
            if ($this->bits[3] == '*') {
                for ($i = 1; $i <= 12; $i++) {
                    $months[] = $i;
                }
            } else {
                $months = $this->expand_ranges($this->bits[3]);
                $months = $this->_sanitize($months, 1, 12);
            }
            $this->months_arr = $months;
        }
        return $this->months_arr;
    }

    /**
     * Assumes that value is not *, and creates an array of valid numbers that
     * the string represents.  Returns an array.
     */
    private function expand_ranges($str) {
        if (strstr($str, ',')) {
            $arParts = explode(',', $str);
            foreach ($arParts AS $part) {
                if (strstr($part, '-')) {
                    $arRange = explode('-', $part);
                    for ($i = $arRange[0]; $i <= $arRange[1]; $i++) {
                        $ret[] = $i;
                    }
                } else {
                    $ret[] = $part;
                }
            }
        } elseif (strstr($str, '-')) {
            $arRange = explode('-', $str);
            for ($i = $arRange[0]; $i <= $arRange[1]; $i++) {
                $ret[] = $i;
            }
        } else {
            $ret[] = $str;
        }
        $ret = array_unique($ret);
        sort($ret);
        return $ret;
    }

    //remove the out of range array elements. $arr should be sorted already and does not contain duplicates
    private function _sanitize($arr, $low, $high) {
        $count = count($arr);
        for ($i = 0; $i <= ($count - 1); $i++) {
            if ($arr[$i] < $low) {
                unset($arr[$i]);
            } else {
                break;
            }
        }

        for ($i = ($count - 1); $i >= 0; $i--) {
            if ($arr[$i] > $high) {
                unset($arr[$i]);
            } else {
                break;
            }
        }

        //re-assign keys
        sort($arr);
        return $arr;
    }

}

class phpcron_list {

    /**
     * @var phpcron_list
     */
    static protected $instance;
    private $CronJobs;
    private $SerialisedCronJobs;

    /**
     *
     * @param phpcron_job $Job
     * @return phpcron
     */
    public function AddJob($Job) {
        $this->CronJobs[get_class($Job)] = $Job;
        return $this;
    }

    /**
     * @return phpcron_job
     */
    public function GetAJob() {
        $job = false;
        foreach ($this->CronJobs as $k => $obj) {
            if ($obj->NextRunTime() === false)
                continue;

            if ($job === false)
                $job = $obj;
            elseif ($job->LastRan > $obj->LastRan)
                $job = $obj;
        }

        return $job;
    }

    public function __sleep() {
        $this->SerialisedCronJobs = array();

        foreach ($this->CronJobs as $k => $obj)
            $this->SerialisedCronJobs[$k] = serialize($obj);

        return array('SerialisedCronJobs');
    }

    public function __wakeup() {
        $this->CronJobs = array();

        foreach ($this->SerialisedCronJobs as $k => $obj)
            $this->CronJobs[$k] = unserialize($obj);
        $this->SerialisedCronJobs = '';
    }

    public function __construct() {
        $this->CronJobs = array();
        $this->SerialisedCronJobs = '';
    }

    /**
     * @return phpcron_list
     */
    static function getinstance() {
        if (!self::$instance)
            self::$instance = new self();
        return self::$instance;
    }

}

abstract class phpcron_job extends phpcron_runtime {

    protected $phpcron;
    public $CronPattern;
    public $lastrun;

    abstract public function RunJob();

    public function NextRunTime() {
        $this->evaluate_job();
        return ($this->lastrun >= $this->lastRan) ? false : $this->lastRan;
    }

    public function __sleep() {
        return array('CronPattern', 'lastrun');
    }

    public function __wakeup() {

    }

    public function __construct() {
        $this->phpcron = phpcron_list::getinstance();
        ;
    }

}