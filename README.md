# Weather Notification System

A cron-based weather alert system built using Core PHP, MySQL, and OpenWeatherMap API.

## Features
- User subscriptions for weather alerts
- Temperature and weather-condition based rules
- Automated cron jobs for weather checks
- Email notifications
- Payment-based subscription validity
- Clean MVC architecture
- Detailed logging

## Tech Stack
- PHP (Core)
- MySQL
- JavaScript
- HTML & CSS
- OpenWeatherMap API
- PHPMailer
- Linux Cron

## Setup Instructions
1. Clone the repository
2. Create MySQL database using `/sql/schema.sql`
3. Configure database credentials in `app/config/db.php`
4. Set up cron job to run `app/cron/check_weather.php`
5. Open `public/subscribe.php` to add subscriptions

## Author
Riya Sisodia