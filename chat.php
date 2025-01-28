<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            height: calc(100vh - 150px);
            margin-top: 20px;
        }
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
        }
        .message {
            margin-bottom: 10px;
            padding: 8px 15px;
            border-radius: 15px;
            max-width: 70%;
        }
        .message.sent {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }
        .message.received {
            background-color: #e9ecef;
            margin-right: auto;
        }
        .user-list {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: white;
        }
        .user-item {
            cursor: pointer;
            padding: 8px;
            border-radius: 5px;
        }
        .user-item:hover {
            background-color: #f8f9fa;
        }
        .selected-user {
            background-color: #e9ecef;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Chat App</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-light">Welcome, <?php echo htmlspecialchars($username); ?></span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container chat-container">
        <div class="row">
            <!-- User List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <input type="text" id="userSearch" class="form-control" placeholder="Search users...">
                    </div>
                    <div class="user-list" id="userList">
                        <!-- Users will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" id="chatHeader">
                        Select a user to start chatting
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <!-- Messages will be loaded here -->
                    </div>
                    <div class="card-footer">
                        <form id="messageForm" class="d-flex">
                            <input type="text" id="messageInput" class="form-control me-2" placeholder="Type your message..." disabled>
                            <button type="submit" class="btn btn-primary" disabled>Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedUser = null;
        let lastMessageId = 0;

        // Search users
        document.getElementById('userSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value;
            fetch(`get_users.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(users => {
                    const userList = document.getElementById('userList');
                    userList.innerHTML = '';
                    users.forEach(user => {
                        if (user.id != <?php echo $user_id; ?>) {
                            const div = document.createElement('div');
                            div.className = 'user-item';
                            div.textContent = user.username;
                            div.onclick = () => selectUser(user);
                            userList.appendChild(div);
                        }
                    });
                });
        });

        // Select user to chat with
        function selectUser(user) {
            selectedUser = user;
            document.querySelectorAll('.user-item').forEach(item => item.classList.remove('selected-user'));
            event.target.classList.add('selected-user');
            document.getElementById('chatHeader').textContent = `Chat with ${user.username}`;
            document.getElementById('messageInput').disabled = false;
            document.getElementById('messageForm').querySelector('button').disabled = false;
            loadMessages();
        }

        // Send message
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!selectedUser) return;

            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            if (!message) return;

            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `receiver_id=${selectedUser.id}&message=${encodeURIComponent(message)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    loadMessages();
                }
            });
        });

        // Load messages
        function loadMessages() {
            if (!selectedUser) return;

            fetch(`get_messages.php?user_id=${selectedUser.id}&last_id=${lastMessageId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.messages.length > 0) {
                        const chatMessages = document.getElementById('chatMessages');
                        data.messages.forEach(msg => {
                            if (msg.id > lastMessageId) {
                                const messageDiv = document.createElement('div');
                                messageDiv.className = `message ${msg.sender_id == <?php echo $user_id; ?> ? 'sent' : 'received'}`;
                                messageDiv.textContent = msg.message;
                                chatMessages.appendChild(messageDiv);
                                lastMessageId = msg.id;
                            }
                        });
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                });
        }

        // Poll for new messages
        setInterval(loadMessages, 3000);

        // Initial user load
        document.getElementById('userSearch').dispatchEvent(new Event('input'));
    </script>
</body>
</html> 