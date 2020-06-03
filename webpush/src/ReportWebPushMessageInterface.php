<?php
/**
 * Created by PhpStorm.
 *  * User: Mohamed MALKI
 * Email: mohamed.malki.cap@gmail.com
 * Date: 03/06/2020
 * Time: 11:08
 */

namespace DoctrineWeb\WebPush;


interface ReportWebPushMessageInterface
{

    /**
     * Handle a message sent report.
     *
     * @param \Minishlink\WebPush\MessageSentReport $report
     * @param \DoctrineWeb\WebPush\PushSubscription $subscription
     * @param string $message
     * @return void
     */
    public function handleReport($report, $subscription, $message);
}