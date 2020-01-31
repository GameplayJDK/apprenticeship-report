<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2020 GameplayJDK
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Service;

use DateTime;

/**
 * Trait DatetimeDayOfWeekTrait
 *
 * @package App\Service
 */
trait DatetimeDayOfWeekTrait
{
    /**
     * @param DateTime $dateTime
     * @param string $day
     * @return bool
     */
    protected function isDayOfWeek(DateTime $dateTime, string $day): bool
    {
        // l (lowercase 'L') = A full textual representation of the day of the week.
        return $dateTime->format('l') === $day;
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isMonday(DateTime $dateTime): bool
    {
        return $this->isDayOfWeek($dateTime, 'Monday');
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isTuesday(DateTime $dateTime): bool
    {
        return $this->isDayOfWeek($dateTime, 'Tuesday');
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isWednesday(DateTime $dateTime): bool
    {
        return $this->isDayOfWeek($dateTime, 'Wednesday');
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isThursday(DateTime $dateTime): bool
    {
        return $this->isDayOfWeek($dateTime, 'Thursday');
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isFriday(DateTime $dateTime): bool
    {
        return $this->isDayOfWeek($dateTime, 'Friday');
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isSaturday(DateTime $dateTime): bool
    {
        return $this->isDayOfWeek($dateTime, 'Saturday');
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isSunday(DateTime $dateTime): bool
    {
        return $this->isDayOfWeek($dateTime, 'Sunday');
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isWeekend(DateTime $dateTime): bool
    {
        return $this->isSaturday($dateTime) || $this->isSunday($dateTime);
    }

    /**
     * @param DateTime $dateTime
     * @return bool
     */
    protected function isWeekday(DateTime $dateTime): bool
    {
        return !$this->isWeekend($dateTime);
    }
}
