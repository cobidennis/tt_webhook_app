<?php

class Webhook {
    private string $url;
    private int $orderId;
    private string $name;
    private string $event;

    public function __construct(string $url, int $orderId, string $name, string $event) {
        $this->url = $url;
        $this->orderId = $orderId;
        $this->name = $name;
        $this->event = $event;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getOrderId() {
        return $this->orderId;
    }

    public function getName() {
        return $this->name;
    }

    public function getEvent() {
        return $this->event;
    }

    public function toArray() {
        return [
            'url' => $this->url,
            'order_id' => $this->orderId,
            'name' => $this->name,
            'event' => $this->event
        ];
    }
}
