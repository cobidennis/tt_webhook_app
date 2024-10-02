<?php

class WebhookProcessor {
    private array $webhooks;
    private array $failCountByEndpoint;

    public function __construct(array $webhooks) {
        $this->webhooks = $webhooks;
        $this->failCountByEndpoint = [];
    }

    public function process() {
        foreach ($this->webhooks as $webhook) {
            // Ensure it's a Webhook object
            if ($webhook instanceof Webhook) {
                $url = $webhook->getUrl();
                if (!isset($this->failCountByEndpoint[$url])) {
                    $this->failCountByEndpoint[$url] = 0;
                }

                if ($this->failCountByEndpoint[$url] < MAX_FAILS_PER_ENDPOINT) {
                    $sender = new WebhookSender($webhook, $this->failCountByEndpoint);
                    $success = $sender->send();
                    if (!$success) {
                        echo "Failed to send webhook Order ID: " . $webhook->getOrderId() . " after retries.\n";
                    }
                } else {
                    echo "Skipping webhook Order ID: " . $webhook->getOrderId() . " for $url Max Retry Reached!.\n";
                }
            } else {
                echo "Invalid Webhook encountered: Skipping";
            }
        }
    }

    public static function loadWebhooksFromFile($filePath) {
        $webhooks = [];
        if (!file_exists($filePath)) {
            echo "Error: File does not exist.\n"; //TODO: throw an Exception instead if time allows
            exit(1);
        }

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $webhooks[] = new Webhook($data[0], intval($data[1]), $data[2], $data[3]);
            }
            fclose($handle);
        }
        array_shift($webhooks); //removes the heading
        return $webhooks;
    }
}