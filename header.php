<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database tables if needed
require_once 'config/database.php';
$database = new Database();
$database->createTables();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PocketLegal Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-item:hover {
            background-color: #f3f4f6;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .gradient-blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-dark {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }
        .gradient-gray {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
        }
        .gradient-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .gradient-orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        .prose {
            max-width: none;
        }
        .prose p {
            margin-bottom: 1rem;
        }
        .prose strong {
            font-weight: 600;
            color: #1f2937;
        }
        .prose em {
            font-style: italic;
            color: #4b5563;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold text-gray-800">PocketLegal</h1>
            </div>
            
            <!-- Search -->
            <div class="p-4">
                <div class="relative">
                    <input type="text" placeholder="Search" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <span class="absolute right-3 top-2 text-xs text-gray-400">Ctrl+K</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-4">
                <a href="index.php" class="sidebar-item flex items-center px-6 py-3 text-gray-700 bg-blue-50 border-r-2 border-blue-500">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <div class="px-6 py-2">
                    <div class="flex items-center justify-between cursor-pointer" onclick="toggleSubmenu('repository')">
                        <div class="flex items-center">
                            <i class="fas fa-folder mr-3 text-gray-600"></i>
                            <span class="text-gray-700">Repository</span>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    <div id="repository-submenu" class="ml-6 mt-2 space-y-1">
                        <a href="#" class="block py-1 text-sm text-gray-600 hover:text-blue-600">Folders</a>
                        <a href="documents.php" class="block py-1 text-sm text-gray-600 hover:text-blue-600">All documents</a>
                        <a href="#" class="block py-1 text-sm text-gray-600 hover:text-blue-600">Template drafts</a>
                    </div>
                </div>
                <a href="insights.php" class="sidebar-item flex items-center px-6 py-3 text-gray-700">
                    <i class="fas fa-chart-line mr-3"></i>
                    Insights
                </a>
                <a href="tasks.php" class="sidebar-item flex items-center px-6 py-3 text-gray-700">
                    <i class="fas fa-tasks mr-3"></i>
                    Tasks
                </a>
                <a href="templates.php" class="sidebar-item flex items-center px-6 py-3 text-gray-700">
                    <i class="fas fa-file-alt mr-3"></i>
                    Templates
                </a>
                <div class="px-6 py-2">
                    <div class="flex items-center justify-between cursor-pointer" onclick="toggleSubmenu('settings')">
                        <div class="flex items-center">
                            <i class="fas fa-cog mr-3 text-gray-600"></i>
                            <span class="text-gray-700">Settings</span>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                    <div id="settings-submenu" class="ml-6 mt-2 space-y-1 hidden">
                        <a href="users-teams.php" class="block py-1 text-sm text-gray-600 hover:text-blue-600">Users & teams</a>
                        <a href="settings.php" class="block py-1 text-sm text-gray-600 hover:text-blue-600">Account</a>
                    </div>
                </div>
                <a href="knowledge-hub.php" class="sidebar-item flex items-center px-6 py-3 text-gray-700">
                    <i class="fas fa-book mr-3"></i>
                    Knowledge hub
                </a>
            </nav>

            <!-- User Profile -->
            <div class="absolute bottom-0 w-64 p-4 border-t">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($user_name); ?></p>
                        <p class="text-xs text-gray-500">MT London</p>
                    </div>
                    <a href="logout.php" class="ml-auto text-gray-400 hover:text-red-500">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>