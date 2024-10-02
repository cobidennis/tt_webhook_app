# TT Webhook App

This is a simple webhook sender application that processes a queue of webhooks, sends them to their respective destinations, and implements a retry mechanism with exponential backoff.

## Features
- Exponential backoff retry mechanism
- Capped retries at 5 attempts per webhook
- Stops sending to an endpoint after 5 consecutive failures
- Dockerized for portability and ease of deployment

## Requirements
- Docker

## How to Run

1. Clone this repository and navigate into the directory:
    ```bash
    git clone <repository-url>
    cd tt_webhook_app
    ```

2. Build the Docker image:
    ```bash
    docker build -t tt_webhook_app .
    ```

3. Run the Docker container:
    ```bash
    docker run --rm tt_webhook_app
    ```

## Files
- `Dockerfile`: The Docker configuration file to build the container.
- `index.php`: The main/entry_point PHP script
- -- classes
    |-- `Webhook.php`: A Webhook class used as DTO to ensure validity of each webhook
    |-- `WebhookSender.php`: Manages sending a webhook with retries and exponential backoff.
    |-- `WebhookProcessor.php`: Handles the queue of webhooks and tracks failures per endpoint.
- `webhooks.txt`: The webhook data file.


## Design Decisions
- **Exponential Backoff**: The retry delay starts at 1 second and doubles with each retry, up to a maximum of 1 minute.
- **Failure Handling**: After 5 failures to a specific endpoint, no more webhooks are sent to that endpoint.

## Security Considerations
- No sensitive data handling is implemented.
- Webhook URLs are assumed to be trusted for this example hence CURL SSL verification is disabled.

## Trade-offs
- Simplicity: The application doesn't include logging to external systems or a persistent database for tracking failures.
- Scalability: For larger-scale implementations, adding a message queue (e.g., RabbitMQ) and persistent storage would be beneficial to ensure idempotency.
