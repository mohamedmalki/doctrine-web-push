<?php
/**
 * Created by PhpStorm.
 *  * User: Mohamed MALKI
 * Email: mohamed.malki.cap@gmail.com
 * Date: 03/06/2020
 * Time: 11:16
 */

namespace DoctrineWeb\WebPush;


use Illuminate\Support\Facades\Log;

class ReportWebPushMessage implements ReportWebPushMessageInterface
{

    /**
     * Handle a message sent report.
     *
     * @param \Minishlink\WebPush\MessageSentReport $report
     * @param \DoctrineWeb\WebPush\PushSubscription $subscription
     * @param \DoctrineWeb\WebPush\WebPushNotification $message
     * @return void
     * @throws \Exception
     */
    public function handleReport($report, $subscription, $message)
    {
        if ($report->isSuccess()) {
            return;
        }

        Log::warning("Notification failed to sent for subscription {$subscription->endpoint}: {$report->getReason()}");

        if ($report->isSubscriptionExpired()) {
            $subscription->delete();
        }
    }
}