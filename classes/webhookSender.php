<?php

class WebhookSender {
    private Webhook $webhook;
    private array $failCountByEndpoint;

    public function __construct(Webhook $webhook, array &$failCountByEndpoint) {
        $this->webhook = $webhook;
        $this->failCountByEndpoint = &$failCountByEndpoint;
    }

    public function send() {
        $retryCount = 0;
        $delay = INITIAL_RETRY_DELAY;
        $url = $this->webhook->getUrl();

        while ($retryCount < MAX_RETRIES && $this->failCountByEndpoint[$url] < MAX_FAILS_PER_ENDPOINT) {
            $success = $this->attemptSend();
            if ($success) {
                echo "Webhook sent successfully: Order ID " . $this->webhook->getOrderId() . "\n";
                return true;
            }

            // Exponential backoff if failed
            $retryCount++;
            sleep($delay);
            $delay = min($delay * 2, MAX_RETRY_DELAY); //min ensures that the delay never exceeds 1 minute
        }

        $this->failCountByEndpoint[$url]++;
        if ($this->failCountByEndpoint[$url] >= MAX_FAILS_PER_ENDPOINT) {
            echo "Stopping further attempts to $url after " . $this->failCountByEndpoint[$url] . " failures\n";
        }
        return false;
    }

    private function attemptSend() {
        $url = $this->webhook->getUrl();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->webhook->toArray()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disable SSL verification (for development or testing only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode == 200;
    }
}
