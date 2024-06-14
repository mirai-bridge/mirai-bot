# Mirai Bridge

Welcome to **Mirai Bridge**, a powerful Telegram bot interface designed to help you effortlessly bridge your crypto assets across multiple networks. This project leverages Laravel and Nutgram to provide a secure, efficient, and seamless trading and swapping experience for users. Whether you are dealing with Ethereum (ETH), Binance Smart Chain (BNB), Ripple (XRP), Arbitrum, The Open Network (TON), or Solana (SOL), Mirai Bridge is here to make your crypto transactions smooth and hassle-free.

## Installation

### Prerequisites

Before you begin, ensure you have met the following requirements:

-   PHP 8.1 or higher
-   Composer
-   Laravel 8 or higher
-   Telegram Bot API Token

### Steps

1. **Clone the repository:**

    ```bash
    git clone https://github.com/mirai-bridge/mirai-bot.git
    cd mirai-bot
    ```

2. **Install dependencies:**

    ```bash
    composer install
    ```

3. **Set up environment variables:**

    Copy the `.env.example` file to `.env` and update the necessary configuration settings, including your database and Telegram Bot API token.

    ```bash
    cp .env.example .env
    ```

4. **Generate application key:**

    ```bash
    php artisan key:generate
    ```

5. **Run database migrations:**

    ```bash
    php artisan migrate
    ```

6. **Start the Laravel development server:**

    ```bash
    php artisan serve
    ```

7. **Start telegram bot:**

    ```bash
    php artisan nutgram:run
    ```

## Configuration

Ensure you have configured the following environment variables in your `.env` file:

-   `TELEGRAM_TOKEN`: Your Telegram bot API token
-   `DB_CONNECTION`: Your database connection (e.g., mysql)
-   `DB_HOST`: Your database host
-   `DB_PORT`: Your database port
-   `DB_DATABASE`: Your database name
-   `DB_USERNAME`: Your database username
-   `DB_PASSWORD`: Your database password

## Usage

Once you have the bot up and running, users can interact with it via Telegram. The bot supports a range of commands to facilitate crypto transactions. Below is a list of the available commands.

## Commands

-   `/start` - Start interacting with the bot and get an overview of available features.
-   `/help` - Display help information and usage instructions.
-   `/new` - Start the process of bridging assets between networks.
-   `/cancel` - Cancel current operation.
-   `/id` - Get your telegram ID.

## Contributing

We welcome contributions to Mirai Bridge! To contribute, follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature-name`).
3. Make your changes.
4. Commit your changes (`git commit -m 'Add some feature'`).
5. Push to the branch (`git push origin feature/your-feature-name`).
6. Open a pull request.

Please make sure to update tests as appropriate.
