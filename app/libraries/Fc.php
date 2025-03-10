<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author	: Mian Saleem
 *  Email	: saleem@retailpremier.com
 *  ==============================================================================
 */

class Fc
{
    public $allDay;
    public $end;
    public $lang       = 'en';
    public $properties = [];
    public $start;

    public $title;

    public function __construct($params)
    {
        $this->lang = $params['lang'];
    }

    public function convert2($string)
    {
        $persinaDigits1   = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $persinaDigits2   = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠'];
        $allPersianDigits = array_merge($persinaDigits1, $persinaDigits2);
        $replaces         = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '9', '8', '7', '6', '5', '4', '3', '2', '1', '0'];
        return str_replace($allPersianDigits, $replaces, $string);
    }

    public function isWithinDayRange($rangeStart, $rangeEnd)
    {
        $eventStart = $this->stripTime($this->start);
        $eventEnd   = isset($this->end) ? $this->stripTime($this->end) : null;
        if (!$eventEnd) {
            return $eventStart < $rangeEnd && $eventStart >= $rangeStart;
        } else {
            return $eventStart < $rangeEnd && $eventEnd > $rangeStart;
        }
    }

    public function load_event($array, $timezone = null)
    {
        $this->title = $array['title'];

        $this->allDay = $this->time_check($array['start']) && (!isset($array['end']) || $this->time_check($array['end']));

        $this->start = $this->parseDateTime($array['start'], $timezone);
        $this->end   = isset($array['end']) ? $this->parseDateTime($array['end'], $timezone) : null;

        foreach ($array as $name => $value) {
            if (!in_array($name, ['title', 'allDay', 'start', 'end'])) {
                $this->properties[$name] = $value;
            }
        }
    }

    public function parseDateTime($string, $timezone = null)
    {
        if ($this->lang == 'ar') {
            $string = $this->convert2($string);
        }
        $date = new DateTime($string);
        return $date;
    }

    public function stripTime($datetime)
    {
        return new DateTime($datetime->format('Y-m-d'));
    }

    public function time_check($date)
    {
        return (bool) ($date == date('Y-m-d 00:00:00', strtotime(substr($date, 0, 10))));
    }

    public function toArray()
    {
        $array          = $this->properties;
        $array['title'] = $this->title;

        if ($this->allDay) {
            $format = 'Y-m-d';
        } else {
            $format = 'c';
        }

        $array['start'] = $this->start->format($format);
        if (isset($this->end)) {
            $array['end'] = $this->end->format($format);
        }

        return $array;
    }
}
