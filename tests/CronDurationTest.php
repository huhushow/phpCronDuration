<?php
use PHPUnit\Framework\TestCase;
use Huhushow\CronDuration\CronDuration;

class CronDurationTest extends TestCase
{
    private $dowNames = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ];

    public function testCanBeCreatedFromValidCronExpression()
    {
        $this->assertInstanceOf(
            CronDuration::class,
            new CronDuration('0 * * * *')
        );

        $this->assertInstanceOf(
            CronDuration::class,
            new CronDuration('0 * * * *', (60*60))
        );

        $this->assertInstanceOf(
            CronDuration::class,
            new CronDuration('0 * * * *', (60*60), time()-600)
        );
    }

    public function testCanNotBeCreatedFromInvalidCronExpression()
    {
        $invalidStr = "invalid";
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("$invalidStr is not a valid CRON expression");
        new CronDuration($invalidStr);
    }

    public function testCanNotBeCreatedFromIncompleteCronExpression()
    {
        $invalidStr = "* * * *";
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("$invalidStr is not a valid CRON expression");
        new CronDuration($invalidStr);
    }

    public function testCanCheckAvailable()
    {
        foreach (range(0,23) as $time) {
            $cur = new DateTime("today");
            $cur->add(new DateInterval("PT{$time}H"));
            $cron = new CronDuration('0 */4 * * *', (4*60*60), $cur->getTimestamp());
            $this->assertTrue($cron->isAvailableNow());
        }

        foreach (array_keys($this->dowNames) as $dow) {
            foreach ($this->dowNames as $key => $name) {
                $cur = new DateTime($name);
                $cron = new CronDuration("0 0 * * $dow", (24*60*60), $cur->getTimestamp());
                $avail = $cron->isAvailableNow();
                if ($key === $dow) {
                    $this->assertTrue($avail);
                } else {
                    $this->assertFalse($avail);
                }
            }
        }
    }
}
