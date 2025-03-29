# Chat System

## Overview

The **Chat System** is a real-time messaging application built using **Laravel 11** and **Laravel Reverb** for WebSockets, running on **PHP 8.2**. It provides a seamless and interactive chat experience with essential features such as instant messaging, voice notes, file sharing, and real-time user status updates.

### Key Features

- **Real-Time Messaging** – Instantly send and receive messages via Laravel Reverb (WebSockets).
- **Voice Messages** – Record and share voice notes with an integrated recording interface.
- **File Attachments** – Upload and preview images before sending.
- **Typing Indicators** – Display real-time typing notifications.
- **User Presence** – Show users’ online/offline status dynamically.
- **Dark Mode** – Toggle between light and dark themes for accessibility.
- **Responsive Design** – Fully optimized for both desktop and mobile devices.
- **Emoji Support** – Use an emoji picker to enhance messaging.
- **Smooth Animations** – Includes fluid transitions for chat interactions.

## Prerequisites

Ensure the following dependencies are installed on your system:

- **PHP** (>= 8.2)
- **Composer** (for dependency management)
- **Node.js** & **npm** (for frontend assets)
- **Laravel 11**
- **MySQL** (or any Laravel-supported database)
- **Redis** (for event broadcasting)
- **Laravel Reverb** (for WebSockets)

## Installation Instructions

### 1. Clone the Repository

Clone the project to your local environment:

```bash
git clone https://github.com/IhabShahtout/Chat-System.git
cd Chat-System
```

### 2. Install Dependencies

Run the following command to install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

### 3. Configure Environment

Copy the example environment file and update the configuration as needed:

```bash
cp .env.example .env
php artisan key:generate
```

Ensure that database credentials and WebSocket settings are correctly set in the `.env` file.

### 4. Run Database Migrations

Set up the database schema and seed initial data:

```bash
php artisan migrate:refresh --seed --seeder=UserSeeder
```

### 5. Create Storage Link

Run the following command to create a symbolic link for storage access:

```bash
php artisan storage:link
```

### 6. Start WebSockets & Development Server

Run WebSockets using Laravel Reverb:

```bash
php artisan reverb:start
```

Compile frontend assets:

```bash
npm run dev
```

Finally, start the Laravel development server:

```bash
php artisan serve
```

## Usage

- Open the application in a browser.
- Register or log in to start chatting.
- Use real-time messaging features like text, voice, file sharing, and emoji support.

### Default Login Credentials

After running migrations and seeding the database, you can log in using the following test accounts:

| Name           | Email            | Password      |
|---------------|------------------|--------------|
| Ihab Salah    | ihab@example.com | password123  |
| Alaa Ahmed    | alaa@example.com | password123  |
| Mohammed Ali  | mohammed@example.com    | password123    |

## Contributing

Contributions are welcome! Feel free to submit a pull request or report any issues in the repository.

## License

This project is open-source and available under the [MIT License](LICENSE).  
