<?php

namespace App;

trait HasStartEndTime
{
    public function formatStartEndDateTime($dateFormat='%Y-%m-%d')
    {
        if ($this->start_time && $this->end_time) {
            if ($this->start_time->formatLocalized($dateFormat) == $this->end_time->formatLocalized($dateFormat)) {
                return sprintf('%s %s–%s', $this->start_time->formatLocalized($dateFormat),
                    $this->start_time->format('H:i'),
                    $this->end_time->format('H:i')
                );
            }
            return sprintf('%s – %s', $this->start_time->formatLocalized($dateFormat),
                $this->end_time->formatLocalized($dateFormat)
            );
        }
        if ($this->start_time) {
            return sprintf('%s %s', $this->start_time->formatLocalized($dateFormat),
                $this->start_time->format('H:i')
            );
        }
        return '';
    }

    public function formatStartEndDate($dateFormat='%Y-%m-%d')
    {
        if ($this->start_time && $this->end_time) {
            if ($this->start_time->formatLocalized($dateFormat) == $this->end_time->formatLocalized($dateFormat)) {
                return $this->start_time->formatLocalized($dateFormat);
            }
            return sprintf('%s – %s', $this->start_time->formatLocalized($dateFormat),
                $this->end_time->formatLocalized($dateFormat)
            );
        }
        if ($this->start_time) {
            return $this->start_time->formatLocalized($dateFormat);
        }
        return '';
    }

}
