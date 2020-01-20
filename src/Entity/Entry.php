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

namespace App\Entity;

use DateTime;

/**
 * Class Entry
 *
 * @package App\Model
 */
class Entry
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $datetimeFrom;

    /**
     * @var DateTime
     */
    private $datetimeTo;

    /**
     * @var string|null
     */
    private $content;

    /**
     * @var string|null
     */
    private $issue;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Entry
     */
    public function setId(int $id): Entry
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDatetimeFrom(): DateTime
    {
        return $this->datetimeFrom;
    }

    /**
     * @param DateTime $datetimeFrom
     * @return Entry
     */
    public function setDatetimeFrom(DateTime $datetimeFrom): Entry
    {
        $this->datetimeFrom = $datetimeFrom;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDatetimeTo(): DateTime
    {
        return $this->datetimeTo;
    }

    /**
     * @param DateTime $datetimeTo
     * @return Entry
     */
    public function setDatetimeTo(DateTime $datetimeTo): Entry
    {
        $this->datetimeTo = $datetimeTo;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Entry
     */
    public function setContent(?string $content): Entry
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIssue(): ?string
    {
        return $this->issue;
    }

    /**
     * @param string|null $issue
     * @return Entry
     */
    public function setIssue(?string $issue): Entry
    {
        $this->issue = $issue;
        return $this;
    }
}
