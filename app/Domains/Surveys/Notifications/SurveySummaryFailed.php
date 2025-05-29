<?php

declare(strict_types=1);

namespace App\Domains\Surveys\Notifications;

use App\Domains\Surveys\Models\SurveySummary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SurveySummaryFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private SurveySummary $summary)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->summary->survey->title;
        $actionUrl = url('/surveys/' . $this->summary->survey_id . '/summaries/' . $this->summary->id);

        return (new MailMessage())
            ->line("The summary generation for survey ({$title}) failed.")
            ->action('View Survey Summary', $actionUrl)
            ->line('Thank you for using our application!');
    }
}
