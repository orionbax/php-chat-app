# Simple PHP Chat Application

A basic chat application built with PHP, MySQL, and JavaScript. This application uses polling to check for new messages and provides a simple user interface for real-time communication.

## Features

- User registration and authentication
- User search functionality
- Real-time messaging using polling
- Clean and responsive design using Bootstrap
- Secure password hashing
- Protection against SQL injection

## Requirements

- PHP 7.0 or higher
- MySQL 5.6 or higher
- XAMPP (or similar local development environment)
- Modern web browser

## Installation

1. Clone or download this repository to your XAMPP's htdocs directory:
   ```
   C:/xampp/htdocs/Chat/
   ```

2. Create a new MySQL database named 'chat_app':
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named 'chat_app'
   - Import the `database.sql` file

3. Configure the database connection:
   - Open `config.php`
   - Update the database credentials if needed (default values should work with XAMPP)

4. Access the application:
   - Open your web browser
   - Navigate to `http://localhost/Chat/`
   - Register a new account
   - Start chatting!

## Usage

1. Register a new account with a username and password
2. Log in with your credentials
3. Use the search bar to find other users
4. Click on a user to start chatting
5. Type your message and press Enter or click Send
6. Messages will update automatically every 3 seconds

## Security Features

- Passwords are securely hashed using PHP's `password_hash()`
- SQL injection prevention using prepared statements
- Session-based authentication
- Input validation and sanitization

## File Structure

- `config.php` - Database configuration and session initialization
- `register.php` - User registration
- `login.php` - User login
- `chat.php` - Main chat interface
- `get_users.php` - API endpoint for user search
- `get_messages.php` - API endpoint for retrieving messages
- `send_message.php` - API endpoint for sending messages
- `logout.php` - User logout
- `database.sql` - Database structure

## Notes

- This is a basic implementation using polling. For production use, consider implementing WebSocket for better real-time communication.
- The polling interval is set to 3 seconds by default. Adjust this value in `chat.php` if needed.
- For production use, additional security measures should be implemented. "# php-chat-app" 
