<?php
/**
 * Stopwatch plugin for Craft CMS 4.x
 *
 * This plugin calculates how much time it takes to read the page and watch the videos
 *
 * @link      percipio.london
 * @copyright Copyright (c) 2022 percipio.london
 */

namespace percipiolondon\stopwatch\models;

use craft\base\Model;
use craft\helpers\DateTimeHelper;

/**
 * Class TimeModel
 *
 * @package percipiolondon\stopwatch\models
 */
class TimeModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var int
     */
    public int $seconds = 0;

    /**
     * @var bool
     */
    public bool $showSeconds = true;

    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->human();
    }

    /**
     * @return string
     */
    public function human(): string
    {
        if($this->seconds === 0) {
            return '0 minutes';
        }

        return DateTimeHelper::humanDuration($this->seconds, $this->showSeconds);
    }

    /**
     * @throws \Exception
     */
    public function interval($format = '%h hours, %i minutes, %s seconds'): string
    {
        $currentTimeStamp = DateTimeHelper::currentTimeStamp();
        $datetimeStart = DateTimeHelper::toDateTime($currentTimeStamp);
        $datetimeEnd = DateTimeHelper::toDateTime(DateTimeHelper::currentTimeStamp() + $this->seconds);

        $interval = $datetimeStart->diff($datetimeEnd);

        return $interval->format($format);
    }

    /**
     * @return int
     */
    public function seconds(): int
    {
        return $this->seconds;
    }

    /**
     * @return int
     */
    public function minutes(): int
    {
        return floor($this->seconds / 60);
    }

    /**
     * @return int
     */
    public function hours(): int
    {
        return floor(($this->seconds /  60) / 60);
    }
}