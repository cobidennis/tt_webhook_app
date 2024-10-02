# Use an official PHP runtime as a parent image
FROM php:8.1-cli

# Install curl (required for sending HTTP requests)
RUN apt-get update && apt-get install -y libcurl4-openssl-dev
RUN docker-php-ext-install curl

# Set the working directory inside the container
WORKDIR /usr/src/app

# Copy the current directory contents into the container at /usr/src/app
COPY . .

# Command to run the PHP script
ENTRYPOINT [ "php", "./index.php" ]

