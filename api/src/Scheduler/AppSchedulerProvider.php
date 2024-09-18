<?php

declare(strict_types=1);

namespace App\Scheduler;

use App\Messenger\Reddit\LoadRedditPostsMessage;
use App\Messenger\Twitter\LoadTweetsMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('default')]
final readonly class AppSchedulerProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(RecurringMessage::cron('0 0 * * *', new LoadTweetsMessage(limit: 250)))
            ->add(RecurringMessage::cron('0 4 * * *', new LoadRedditPostsMessage(limit: 250)))
        ;
    }
}
