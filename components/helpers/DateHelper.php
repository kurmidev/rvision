<?php

namespace app\components\helpers;

use app\components\helpers\Utils as U;

class DateHelper {

    const DATE_FORMAT = 'Y-m-d';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    const DAYS_IN_MONTH = 30;
    const DAYS_IN_MONTH_CONSIDER = DAYS_IN_MONTH_CONSIDER;
    const CONSIDER_MONTH = false;

    static function getFinancialYear($date = null) {
        $time = strtotime($date ?: date(self::DATE_FORMAT));
        $y = date('y', $time);
        $m = date('m', $time);
        if ($m < 4) {
            return ($y - 1) . '-' . $y;
        } else {
            return ($y) . '-' . ($y + 1);
        }
    }

    static function getMaxHexDatetimeAsTimestamp() {
        return self::dateObjectFromTime(hexdec('FFFFFFFF'), self::DATE_TIME_FORMAT);
    }

    static function getMicrotime() {
        return Utils::getTimeStamp('Uu');
    }

    static function getTime() {
        return time();
    }

    static function getTimeZone() {
        $date = new \DateTime();
        $tz = $date->getTimezone();
        return $tz->getName();
    }

    static function timetoIsoDate($timestamp) {
        return new \MongoDB\BSON\UTCDateTime(($timestamp * 1000));
    }

    static function toIsoDate($date) {
        if ($date instanceof \MongoDB\BSON\UTCDateTime) {
            return $date;
        } else {
            $timestamp = $date;
            if (!is_numeric($date)) {
                $timestamp = strtotime($date);
            }
            return new \MongoDB\BSON\UTCDateTime(($timestamp * 1000));
        }
    }

    public static function getMongoTimeStamp($asInt = false) {
        $obj = new \MongoDB\BSON\UTCDateTime();
        if ($asInt) {
            return ((string) $obj) + 0;
        } else {
            return $obj;
        }
    }

    public static function getMiliSec() {
        $obj = new \MongoDB\BSON\UTCDateTime();
        return ((string) $obj) + 0;
    }

    static function dateIsoObject($date) {
        return new \MongoDB\BSON\UTCDateTime(self::dateObject($date));
    }

    static function isaPastDate($date, $format = self::DATE_FORMAT, $opeartor = '<=') {
        return self::compareDate($date, self::getDate($format), $opeartor);
    }

    static function isaFutureDate($date, $format = self::DATE_FORMAT, $opeartor = '>') {
        return self::compareDate($date, self::getDate($format), $opeartor);
    }

    static function compareDate($dateOne, $dateTwo, $operator = '=') {
        $date1 = new \DateTime($dateOne);
        $date2 = new \DateTime($dateTwo);
        switch ($operator) {
            case "<=":
                return $date1 <= $date2;
            case ">=":
                return $date1 >= $date2;
            case "<":
                return $date1 < $date2;
            case ">":
                return $date1 > $date2;
            case "=":
            default:
                return $date1 == $date2;
        }
    }

    static function getDateToInt($dateOne) {
        return date('Ymd', strtotime($dateOne)) + 0;
    }

    static function getMaxdate($dateOne, $dateTwo, $format = self::DATE_FORMAT) {
        if (!$dateOne) {
            return $dateTwo;
        } elseif (!$dateTwo) {
            return $dateOne;
        }
        $date1 = self::dateObject($dateOne);
        $date2 = self::dateObject($dateTwo);
        return $date1 > $date2 ? $date1->format($format) : $date2->format($format);
    }

    static function getMindate($dateOne, $dateTwo, $format = self::DATE_FORMAT, &$twoSelected = null) {
        if (!$dateOne) {
            return $dateTwo;
        } elseif (!$dateTwo) {
            return $dateOne;
        }
        $date1 = self::dateObject($dateOne);
        $date2 = self::dateObject($dateTwo);
        if ($date1 < $date2) {
            $ret = $date1->format($format);
            $twoSelected = false;
        } else {
            $ret = $date2->format($format);
            $twoSelected = true;
        }
        return $ret;
    }

    static function getDateTime($format = self::DATE_TIME_FORMAT) {
        return date($format);
    }

    static function getDate($format = self::DATE_FORMAT) {
        return date($format);
    }

    static function formatDate($date, $format = self::DATE_FORMAT) {
        $date = new \DateTime($date);
        return $date->format($format);
    }

    static function gmdate($date) {
        return gmdate('c', strtotime($date));
    }

    static function formatDateTime($date, $format = self::DATE_TIME_FORMAT) {
        return self::formatDate($date, $format);
    }

    static function dateAdd($start, $days, $months = 0, $years = 0, $format = self::DATE_FORMAT) {
        $date = new \DateTime($start);
        if (CALCULATE_30_DAY_MONTH == true) {
            $days = ($years * self::DAYS_IN_MONTH * 12) + ($months * self::DAYS_IN_MONTH) + $days;
            $interval = str_replace(['+', '-'], '', "P{$days}D");
        } else {
            $interval = str_replace(['+', '-'], '', "P{$years}Y{$months}M{$days}D");
        }
        $date->add(new \DateInterval($interval));
//        if($months>0){
//            $date->sub(new \DateInterval("P0Y0M1D"));
//        }
        return $date->format($format);
    }

    static function _dateAdd($start, $interval, $format = self::DATE_TIME_FORMAT) {
        $date = new \DateTime($start);
//        $days = $days; //+ ($months * \components\Constants::DAYS_IN_MONTH);
//        \components\helper\Utils::l($date);
//        exit;
        $date->add(new \DateInterval("P" . trim($interval, 'P') . ""));
//        if($months>0){
//            $date->sub(new \DateInterval("P0Y0M1D"));
//        }
        return $format ? $date->format($format) : $date;
    }

    static function dateSub($start, $days, $months = 0, $years = 0, $format = self::DATE_FORMAT) {
        $date = new \DateTime($start);
        $days = $days; //+ ($months * \components\Constants::DAYS_IN_MONTH);
        $date->sub(new \DateInterval("P{$years}Y{$months}M{$days}D"));
//        if($months>0){
//            $date->sub(new \DateInterval("P0Y0M1D"));
//        }
        return $date->format($format);
    }

    static function dateTotime($date) {
        return (string) strtotime($date);
    }

    static function dateTotimeStamp($mongoTime) {
        $format = 'U';
        if (!empty($mongoTime)) {
            return ((( $mongoTime instanceof \MongoDB\BSON\UTCDateTime ) ? $mongoTime->toDateTime()->format($format) : (isset($mongoTime['date']) ? $mongoTime['date'] : self::dateObjectFromTime($mongoTime, $format)))) + 0;
        }
        return "";
    }

    static function mongoTsToDateTime($mongoTime, $format = \components\helper\DateHelper::DATE_TIME_FORMAT . '.u') {
        return (( $mongoTime instanceof \MongoDB\BSON\UTCDateTime ) ? $mongoTime->toDateTime()->setTimezone(new \DateTimeZone(TIME_ZONE))->format($format) : (isset($mongoTime['date']) ? $mongoTime['date'] : \components\helper\DateHelper::dateObjectFromTime($mongoTime, $format)));
    }

    static function dateObjectFromTime($start, $format = null) {
        return self::dateObject($start, $format);
    }

    static function dateObject($start, $format = null) {
        $date = new \DateTime();
        if (is_numeric($start)) {
            $date->setTimestamp($start);
        } else {
            $date->setTimestamp(strtotime($start));
        }
        if ($format) {
            return $date->format($format);
        }
        return $date;
    }

    static function datetimeAdd($start, $sec, $min = 0, $hours = 0, $format = self::DATE_TIME_FORMAT) {
        if (is_float($min)) {
            $sec = 60 * $min;
            $min = ($sec / 60) % 60;
            $sec = $sec % 60;
        }
        $date = self::dateObject($start);
        $date->add(new \DateInterval("P0Y0M0DT{$hours}H{$min}M{$sec}S"));
        return $date->format($format);
    }

    static function datetimeSub($start, $sec, $min = 0, $hours = 0, $format = self::DATE_TIME_FORMAT) {
        if (is_float($min)) {
            $sec = 60 * $min;
            $min = ($sec / 60) % 60;
            $sec = $sec % 60;
        }
        $date = self::dateObject($start);
        $date->sub(new \DateInterval("P0Y0M0DT{$hours}H{$min}M{$sec}S"));
        return $date->format($format);
    }

    static function date1secLess($start, $sec = 1, $format = self::DATE_TIME_FORMAT) {

        $date = self::dateObject($start);
        $date->sub(new \DateInterval("P0Y0M0DT{$sec}S"));
        return $date->format($format);
    }

    static function getTimeString($seconds, $long = true, $glue = ' ') {
        $H = floor($seconds / 3600);
        $i = ($seconds / 60) % 60;
        $s = (double) ($seconds % 60.00);

        $ret = [];
        if ($H > 0) {
            $ret[] = $H . ($long ? ' Hours' : '');
        }
        if ($i > 0) {
            $ret[] = $i . ($long ? ' minutes' : '');
        }
        if ($s > 0) {
            $ret[] = $s . ($long ? ' seconds' : '');
        } else {
            $ret[] = $seconds . ($long ? ' seconds' : '');
        }
        return implode($glue, $ret);
    }

    static function getDiffdays($start, $end, &$diff = false, $format = '%R%a') {
//        \yii::debug(['getDiffdays', $start, $end], 'DATEDIFF');
        $sDate = new \DateTime($start);
        $eDate = new \DateTime($end);
        $diff = $sDate->diff($eDate);

//        \Yii::debug(['s' => $start, 'e' => $end, 'd' => $diff, $diff->format('%R%a')], 'getDiffdays1');
        return $diff->format($format);
    }

    static function getDiffMonthDaysBtwDates($start, $end, &$fulldays = 0) {
        $sDate = date_create($start);
        $eDate = date_create($end);
        $month = 0;
        $maxloop = 120;
        $st = $sDate;
        $diff = $st->diff($eDate)->format('%R%a');
        $year = $st->diff($eDate)->format('%y');
        $i = 0;
        if ($diff > 0) {
            $_days = $fulldays = $diff;
        } else {
            $_days = $fulldays = 0;
        }
//        U::l(">>>diff=" . $diff);
//        U::l(">>>ST1=" . $st->format('Y-m-d'));
        while ($diff > 0) {
            $interval = new \DateInterval('P1M');
            $st = $st->add($interval);
            $diff = $st->diff($eDate)->format('%R%a');
//            U::l(">>>DDD=" . $diff);
//            U::l(">>>ST=" . $st->format('Y-m-d'));
//            U::l(">>>ED=" . $eDate->format('Y-m-d'));
            if ($diff >= 0) {
                $month += 1;
            }
            $i++;
            if ($maxloop > 0 && $i > $maxloop) {
                break;
            }
        }
        if ($month > 0) {
            $_days = $st->diff($eDate)->format('%R%a');
        }
        if ($_days < 0) {
            $_days = ($eDate->format('t') + $_days) + 0;
        } else {
            $_days = $_days + 0;
        }

        return [$month, $_days, $year];
    }

    static function getDiffSec($start, $end, &$diff = false) {
//        \yii::debug(['getDiffdays', $start, $end], 'DATEDIFF');
        $sDate = new \DateTime($start);
        $eDate = new \DateTime($end);
        $diff = $sDate->diff($eDate);
        $days = $diff->format('%R%s');
//        \Yii::debug(['s' => $start, 'e' => $end, 'd' => $diff, $diff->format('%R%a')], 'getDiffdays1');
        return $diff->format('%R%s');
    }

    static function getDiffTotalSec($start, $end) {
//        \yii::debug(['getDiffdays', $start, $end], 'DATEDIFF');
        $sDate = new \DateTime($start);
        $eDate = new \DateTime($end);
        $diff = $eDate->getTimestamp() - $sDate->getTimestamp();

        return $diff;
    }

    public static function getMonthDaysfromRange($start, $end, &$days = null) {

        if (CALCULATE_30_DAY_MONTH == false) {
            $return = self::getDiffMonthDaysBtwDates($start, $end, $days);
            return $return;
        } else {
            $diff = null;
            $days = DateHelper::getDays($start, $end, $diff);
            if (self::CONSIDER_MONTH) {
                return [$diff->y * 12 + $diff->m, $diff->d];
            } else {
                return [floor($days / self::DAYS_IN_MONTH), $days % self::DAYS_IN_MONTH];
            }
        }
    }

    public static function getMonthDaysDays($month, $days) {

        return ($month * self::DAYS_IN_MONTH) + $days;
    }

    static function getDays($start, $end, &$diff = false) {
//        \yii::debug(['getDiffdays', $start, $end], 'DATEDIFF');
        $sDate = new \DateTime($start);
        $eDate = new \DateTime($end);
        $diff = $sDate->diff($eDate);
        return $diff->days;
    }

    static function getInterval($start, $end, $onlyDate = false) {
        $sDate = new \DateTime($start);
        $eDate = new \DateTime($end);
//        return [$sDate,$eDate];
        $interval = $sDate->diff($eDate);
        $suffix = ( $interval->invert ? ' ago' : '' );
        $return = [];
        if ($v = $interval->y >= 1)
            $return[] = self::pluralize($interval->y, 'year');

        if ($v = $interval->m >= 1)
            $return[] = self::pluralize($interval->m, 'month');

        if ($v = $interval->d >= 1)
            $return[] = self::pluralize($interval->d, 'day');
        if ($onlyDate == false) {
            if ($v = $interval->h >= 1)
                $return[] = self::pluralize($interval->h, 'hour');

            if ($v = $interval->i >= 1)
                $return[] = self::pluralize($interval->i, 'minute');

            $return[] = self::pluralize($interval->s, 'second');
        }
        return implode(' ', $return);
    }

    static function pluralize($count, $text) {
        return $count . ( ( $count == 1 ) ? ( " $text" ) : ( " ${text}s" ) );
    }

    static function getDiffMonthdays($start, $end) {

        if (CALCULATE_30_DAY_MONTH == false) {
            list($m, $d, $y) = self::getDiffMonthDaysBtwDates($start, $end);
            return self::getYMDStr2($d, $m, $y);
        } else {

            $interval = null;
//        return self::getInterval($start, $end, true);
            $daysLeft = self::getDiffdays($start, $end, $interval);
            if ($daysLeft > 0) {
                return self::getYMDStr($daysLeft);
            } else {
                return 0;
            }
        }
    }

    static function getDiffMonth($start, $end) {
        $interval = null;
//        return self::getInterval($start, $end, true);
        $daysLeft = self::getDiffdays($start, $end, $interval);
        return $interval->m;
    }

    static function getMonthsdays($days) {
        $month = floor($days / \components\Constants::DAYS_IN_MONTH);
        $days = floor($days % \components\Constants::DAYS_IN_MONTH);
        return [$month, $days];
    }

    static function getYMDStr($days) {
        $ret = [];
        if ($year = floor($days / (\components\Constants::DAYS_IN_MONTH * 12))) {
            $ret[] = self::pluralize($year, 'Year');
        }
        if ($month = floor($days / \components\Constants::DAYS_IN_MONTH) % 12)
            $ret[] = self::pluralize($month, 'Month');

        if ($days = floor($days % \components\Constants::DAYS_IN_MONTH))
            $ret[] = self::pluralize($days, 'day');

        return implode(' ', $ret);
    }

    static function getYMDStr2($days, $month, $year) {
        $ret = [];
        if ($year) {
            $ret[] = self::pluralize($year, 'Year');
        }
        if ($month)
            $ret[] = self::pluralize($month, 'Month');

        if ($days)
            $ret[] = self::pluralize($days, 'day');

        return implode(' ', $ret);
    }

    static function validateDate($date, $format = self::DATE_FORMAT) {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && ($format == null || $d->format($format) == $date);
    }

    static function isValidDate($date) {
        $d = self::dateObject($date);
        return $d ? true : false;
    }

    static function getEndofDay($date, $force = 0) {
        $d = self::dateObject($date);
        if ($d->format('s') != '0' && $force == 0) {
            return $d->format(self::DATE_TIME_FORMAT);
        } else {
            return $d->format('Y-m-d 23:59:59');
        }
    }

    static function getmonthDays($date) {
        return date('t', strtotime($date));
    }

    static function getDateObject($interval = null, $sub = false, $baseDate = null) {
        $date = new \DateTime($baseDate);
        if ($interval) {
            if ($sub)
                $date->sub(new \DateInterval($interval));
            else
                $date->add(new \DateInterval($interval));
        }
        return $date;
    }

    static function getDateInBetween($start, $end) {
        $days = self::getDiffdays($start, $end) + 0;
        if ($days > 0) {
            $date = new \DateTime($start);
            $ret = [];
            $interval = new \DateInterval('P1D');
            $d = $date->format('Y-m-d');
            $ret[$d] = $d;
            for ($i = 0; $i < $days; $i++) {
                $d = $date->add($interval)->format('Y-m-d');
                $ret[$d] = $d;
            }
            return array_values($ret);
        } else {
            return [$start, $end];
        }
    }

    /**
     * get First date of month
     */
    static function getFDM($date, $format = self::DATE_TIME_FORMAT) {
        $date = new \DateTime($date);
        $date->modify('first day of this month');
        return $date->format($format);
    }

    /**
     * get last date of month
     */
    static function getLDM($date, $format = self::DATE_TIME_FORMAT) {
        $date = new \DateTime($date);
        $date->modify('last day of this month');
        $date->setTime(23, 59, 59);
        return $date->format($format);
    }

    static function reportDateFormat($reportDate) {
        if (is_array($reportDate)) {
            foreach ($reportDate as $rd) {
                $return[] = date('Ymd', strtotime($rd)) + 0;
            }
            return $return;
        } else {
            return date('Ymd', strtotime($reportDate)) + 0;
        }
    }

    static function getTZDateTime($date) {
        return date("Y-m-d\TH:i:s\Z", strtotime($date));
    }

    static function getActExpDate($s, $e) {
        $sd = date('Y-m-d', strtotime($s));
        $ed = date('Y-m-d', strtotime($e));
        if (self::compareDate($ed, $s, '>')) {
            $ed = DateHelper::date1secLess($ed, 1, 'Y-m-d');
        }
        return $sd . ' to ' . $ed;
    }

    static function beginTime($description) {
        if ($description !== false) {
            echo "    > $description ...";
        }
        return microtime(true);
    }

    static function endTime($time, $message = '') {
        $timeTaken = sprintf('%.3f', microtime(true) - $time);
        if ($message === false) {
            return $timeTaken;
        } else {
            echo $message . "\t" . ' done (time: ' . $timeTaken . "s)" . PHP_EOL;
        }
    }

    static function getMonthBtwDate($s, $e, $addSec = 1) {
        $months = array();
        while (strtotime($s) <= strtotime($e)) {
            $m = date('m', strtotime($s));
            $months[$m] = $m + 0;
            $s = date('01 M Y', strtotime($s . '+ 1 month')); // Set date to 1 so that new month is returned as the month changes.
        }
        return array_values($months);
    }

    static function getMonthBtwDate2($s, $e, $addSec = 1) {
        $startDate = new \DateTime($s);
        $endDate = new \DateTime($e);
        if ($addSec) {
            $endDate->add(new \DateInterval('PT1M'));
        }
        $dateInterval = new \DateInterval('P1M');
        $datePeriod = new \DatePeriod($startDate, $dateInterval, $endDate);
        \Yii::debug(json_encode($datePeriod), '$datePeriod');
        $months = [];
        foreach ($datePeriod as $date) {
            $m = $date->format('m') + 0;
            $months[$m] = $m;
        }
        return array_values($months);
    }

}
