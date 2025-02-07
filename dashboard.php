<?php
session_start();
include("conn.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Fetch user details
$stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencil Business Management</title>
    <style>
        :root {
            --primary-color: #ff6b6b;
            --secondary-color: #4ecdc4;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding-top: 80px;
            background-color: #f5f6fa;
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .search-bar {
            flex: 0 1 600px;
            margin: 0 20px;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 16px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
        }

        .product-price {
            color: var(--primary-color);
            font-size: 1.2em;
            font-weight: bold;
            margin: 10px 0;
        }

        /* Chat System */
        .chat-tab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--secondary-color);
            color: white;
            padding: 15px 25px;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .chat-modal {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 350px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            display: none;
        }

        .chat-header {
            background: var(--secondary-color);
            color: white;
            padding: 15px;
            border-radius: 15px 15px 0 0;
        }

        .chat-messages {
            height: 300px;
            overflow-y: auto;
            padding: 15px;
        }

        .message-input {
            display: flex;
            padding: 15px;
            border-top: 1px solid #eee;
        }

        .message-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin-right: 10px;
        }
        
    @media (max-width: 768px) {
        .header {
            flex-wrap: wrap;
            padding: 8px 15px;
        }

        .search-bar {
            flex: 1 1 100%;
            order: 3;
            margin: 10px 0 0 0;
        }

        h1 {
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .user-info {
            font-size: 0.9rem;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            padding: 15px;
        }

        .product-image {
            height: 150px;
        }

        .chat-modal {
            width: 90%;
            right: 5%;
            bottom: 70px;
        }
    }

    @media (max-width: 480px) {
        body {
            padding-top: 120px;
        }

        .header {
            padding: 8px 10px;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
        }

        .product-grid {
            grid-template-columns: 1fr;
        }

        .product-card {
            margin: 0 10px;
        }

        .chat-tab {
            right: 10px;
            bottom: 10px;
            padding: 12px 20px;
            font-size: 0.9rem;
        }

        .message-input button {
            padding: 10px 15px;
            font-size: 0.9rem;
        }
    }

    /* Touch-friendly elements */
    button, .product-card {
        cursor: pointer;
        touch-action: manipulation;
    }

    /* Prevent zoom on input */
    @media screen and (max-width: 768px) {
        input, textarea {
            font-size: 16px;
        }
    }

    /* Better mobile navigation */
    .product-info button {
        width: 100%;
        padding: 12px;
        font-size: 1rem;
    }

    /* Chat message responsiveness */
    .message-input {
        gap: 8px;
    }

    .message-input input {
        min-width: 0; /* Fix flexbox overflow */
    }

    /* Hide email on smallest screens */
    @media (max-width: 360px) {
        .user-info span {
            max-width: 120px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    </style>
</head>
<body>
    <!-- Fixed Header -->
    <header class="header">
        <h1>Pencil Marketplace</h1>
        <div class="search-bar">
            <input type="text" placeholder="Search products..." id="searchInput">
        </div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($user['email']); ?></span>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </header>

    <!-- Product Grid -->
    <div class="product-grid" id="productContainer">
        <!-- Products will be loaded here via AJAX -->
    </div>

    <!-- Chat System -->
    <div class="chat-tab" onclick="toggleChat()">Chat with Admin</div>
    <div class="chat-modal" id="chatModal">
        <div class="chat-header">
            <h3>Admin Support</h3>
        </div>
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be loaded here -->
        </div>
        <div class="message-input">
            <input type="text" placeholder="Type your message..." id="messageInput">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        // Load products on page load
        window.onload = function() {
            loadProducts();
        };

        // Product loading
        async function loadProducts(search = '') {
            const response = await fetch(`get_products.php?search=${encodeURIComponent(search)}`);
            const products = await response.json();
            
            const container = document.getElementById('productContainer');
            container.innerHTML = products.map(product => `
                <div class="product-card">
                    <img src="${product.image}" class="product-image" alt="${product.name}">
                    <div class="product-info">
                        <h3>${product.name}</h3>
                        <p class="product-price">$${product.price}</p>
                        <p>${product.description}</p>
                        <button>View Details</button>
                    </div>
                </div>
            `).join('');
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            loadProducts(e.target.value);
        });

        // Chat system
        let chatOpen = false;
        function toggleChat() {
            const modal = document.getElementById('chatModal');
            chatOpen = !chatOpen;
            modal.style.display = chatOpen ? 'block' : 'none';
            if(chatOpen) loadMessages();
        }

        async function loadMessages() {
            const response = await fetch('get_messages.php');
            const messages = await response.json();
            const chatDiv = document.getElementById('chatMessages');
            chatDiv.innerHTML = messages.map(msg => `
                <div class="message ${msg.sender === 'user' ? 'user-message' : 'admin-message'}">
                    <p>${msg.message}</p>
                    <small>${new Date(msg.timestamp).toLocaleTimeString()}</small>
                </div>
            `).join('');
        }

        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if(message) {
                await fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({message})
                });
                
                input.value = '';
                loadMessages();
            }
        }
    </script>
</body>
</html>