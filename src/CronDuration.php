<?php

declare(strict_types=1);

namespace Huhushow\CronDuration;

use DateTime;
use DateTimeZone;
use DateInterval;
use Cron\CronExpression;

class CronDuration
{
    /**
     * @var DateTime
     */
    private $startAt;

    /**
     * @var DateTime
     */
    private $endAt;

    /**
     * @var DateTime
     */
    private $curDt;

    /**
     * @var int
     */
    private $curTs = 0;

    /**
     * @var DateTimeZone
     */
    private $tz;
    private $duration;
    private $cron;

    /**
     * CronUtil constructor.
     * @param string $string cron expression string
     * @param int $duration
     * @param int $curTs
     */
    public function __construct(
        string $expression,
        int $duration = 1,
        int $curTs = 0,
        string $tz = 'Etc/UTC'
    ) {
        $this->tz = new DateTimeZone($tz);
        if (!$curTs) {
            $curTs = time();
        }
        $this->curTs = $curTs;
        $this->curDt = new DateTime();
        $this->curDt->setTimestamp($this->curTs);
        $this->curDt->setTimezone($this->tz);

        $this->duration = $duration - 1;
        $this->cron = new CronExpression($expression);
        $due = false;
        $due = $this->cron->isDue($this->curDt, $this->tz->getName());
        if ($due) {
            $this->startAt = clone $this->curDt;
        } else {
            $this->startAt = $this->cron->getPreviousRunDate(
                $this->curDt, 
                0, 
                false, 
                $this->tz->getName()
            );
        }
        $this->endAt = clone $this->startAt;
        $this->endAt->add(new DateInterval("PT{$this->duration}S"));
    }

    public function isAvailableNow() :bool
    {
        return $this->startAt->getTimestamp() <= $this->getCurTs() && $this->endAt->getTimestamp() >= $this->getCurTs();
    }

    /**
     * @return int
     */
    public function getStartAt() :int
    {
        return $this->startAt->getTimestamp();
    }

    /**
     * @return int
     */
    public function getEndAt() :int
    {
        return $this->endAt->getTimestamp();
    }

    private function getCurTs() :int
    {
        if (!$this->curTs) {
            return time();
        }
        return $this->curTs;
    }

    private function getCurDt() :DateTime
    {
        return DateTime::createFromFormat('U', $this->getCurTs(), $this->tz);
    }
}
