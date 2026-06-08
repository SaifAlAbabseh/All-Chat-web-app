# All-Chat Web App

All-Chat is a real-time web-based chat application. It features a PHP backend for user management, messaging logic, and API endpoints, paired with a Node.js WebSocket server to deliver real-time communication.

## 📁 Project Structure

The project is structured into several key components:

- **Root Directory**: Contains main PHP scripts for various chat actions (e.g., `sendMessage.php`, `addMemberToGroup.php`, `syncChat.php`).
- **`.env`**: Configuration file for database credentials, mailing settings, WebSocket, and API configurations.
- **`DB/`**: Contains database-related files, including the database schema/dump (`allchat.sql`) and connection configuration (`DB.php`).
- **`socket-server/`**: A Node.js application running the WebSocket server (`server.js`) for real-time messaging.
- **`api/`**: API endpoints for app functionality.
- **`mail/` & `email_templates/`**: Handling email sending and templates.
- **`uploads/`**: Directory where uploaded files and media are stored.
- **`Main/`, `Mobile/`, `Extra/`**: Frontend views and UI components.
- **`scripts/`**: Client-side JavaScript files.

## 🚀 Prerequisites

To run this project locally, you will need:
- **Web Server environment**: XAMPP, WAMP, or any environment supporting **PHP** and **MySQL/MariaDB**.
- **Node.js & npm**: Required to run the real-time WebSocket server.

## ⚙️ Installation & Setup

Follow these steps to get the project up and running on your local machine:

### 1. Place the Project in Web Root
Ensure the project folder (`All-Chat-web-app`) is placed inside your web server's root directory (e.g., `htdocs` for XAMPP or `www` for WAMP).

### 2. Database Setup
1. Open your MySQL management tool (e.g., phpMyAdmin at `http://localhost/phpmyadmin`).
2. Create a new database named `allchat`.
3. Import the provided SQL dump file located at `DB/allchat.sql` into the newly created database.

### 3. Environment Configuration
Locate the `.env` file in the root directory. Update the configuration settings to match your local environment if necessary:
```env
PUBLIC_SITE_URL=All-Chat-web-app
DB_HOST_NAME=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_NAME=allchat
WS_PORT=8080
PHP_APP_URL=http://localhost
```
*(Ensure the database credentials (`DB_USERNAME`, `DB_PASSWORD`) and URLs match your setup)*

### 4. WebSocket Server Setup
The real-time chat functionality requires the Node.js WebSocket server to be running.
1. Open a terminal or command prompt.
2. Navigate to the `socket-server` directory:
   ```bash
   cd path/to/All-Chat-web-app/socket-server
   ```
3. Install the required Node.js dependencies (`ws`, `mysql2`, `dotenv`):
   ```bash
   npm install
   ```

## ▶️ Running the Application

To use the application, both your PHP server and the Node.js WebSocket server must be running:

1. **Start Apache and MySQL** via your XAMPP/WAMP control panel.
2. **Start the WebSocket server**:
   In your terminal, within the `socket-server` directory, run:
   ```bash
   npm start
   ```
   *(This runs `node server.js` and listens on the port defined in `.env`)*
3. **Access the Web App**:
   Open your web browser and navigate to the application URL:
   ```text
   http://localhost/All-Chat-web-app/
   ```

You are now ready to use All-Chat!
