<?php

//Define required constants for the retry
const INITIAL_RETRY_DELAY = 1;  // 1 second
const MAX_RETRY_DELAY = 60;     // 1 minute
const MAX_RETRIES = 5;          // Retry limit per webhook
const MAX_FAILS_PER_ENDPOINT = 5;  // Stop sending after 5 failures per endpoint
const WEBHOOKS_FILE_PATH = 'webhooks.txt';  // webhoos.txt Todo: allow addin as argument to script

//include required classes
require_once 'classes/webhook.php';
require_once 'classes/WebhookProcessor.php';
require_once 'classes/webhookSender.php';

//load webhooks
$webhooks = WebhookProcessor::loadWebhooksFromFile(WEBHOOKS_FILE_PATH);
$processor = new WebhookProcessor($webhooks); //Initialize the processor
$processor->process(); //process webhooks

