<?php
/**
 * Created by PhpStorm.
 *  * User: Mohamed MALKI
 * Email: mohamed.malki.cap@gmail.com
 * Date: 03/06/2020
 * Time: 11:05
 */

namespace DoctrineWeb\WebPush;

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushNotification
{
    /**
     * @var \Minishlink\WebPush\WebPush
     */
    protected $webPush;

    /**
     * @var \DoctrineWeb\WebPush\ReportWebPushMessageInterface
     */
    protected $reportHandler;

    /**
     * @param WebPush $webPush
     * @param ReportWebPushMessageInterface $reportHandler
     */
    public function __construct(WebPush $webPush, ReportWebPushMessageInterface $reportHandler)
    {
        $this->webPush = $webPush;
        $this->reportHandler = $reportHandler;
    }

    /**
     * Send the given notification.
     *
     * @param $subscriptions
     * @param null $payload
     * @param array $options
     * @return void
     * @throws \ErrorException
     */
    public function send($subscriptions, $payload = null, array $options = [])
    {
        if (empty($subscriptions)) {
            return;
        }

        foreach ($subscriptions as $subscription) {
            $this->webPush->sendNotification(new Subscription(
                $subscription->endpoint,
                $subscription->public_key,
                $subscription->auth_token,
                $subscription->content_encoding
            ), $payload, false, $options);
        }

        $reports = $this->webPush->flush();

        $this->handleReports($reports, $subscriptions, $payload);
    }

    /**
     * Handle the reports.
     *
     * @param  \Generator $reports
     * @param  \Illuminate\Database\Eloquent\Collection $subscriptions
     * @param  string $message
     * @return void
     */
    protected function handleReports($reports, $subscriptions, $message)
    {
        foreach ($reports as $report) {
            if ($report && $subscription = $this->findSubscription($subscriptions, $report)) {
                $this->reportHandler->handleReport($report, $subscription, $message);
            }
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $subscriptions
     * @param \Minishlink\WebPush\MessageSentReport $report
     * @return mixed
     */
    protected function findSubscription($subscriptions, $report)
    {
        foreach ($subscriptions as $subscription) {
            if ($subscription->endpoint === $report->getEndpoint()) {
                return $subscription;
            }
        }
    }
}
