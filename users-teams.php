<?php
include_once 'header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_name = $_SESSION['user_name'] ?? 'User';
?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <nav class="text-sm text-gray-500 mb-2">
                        <span>Pocketlaw</span> / <span>Users & teams</span>
                    </nav>
                    <h1 class="text-2xl font-semibold text-gray-800">Users & teams</h1>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openInviteModal()" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Invite user
                    </button>
                    <button onclick="openTeamModal()" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create team
                    </button>
                </div>
            </div>
        </div>

        <!-- Users & Teams Content -->
        <div class="p-8">
            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <button onclick="switchTab('users')" id="usersTab" class="tab-btn active py-2 px-1 border-b-2 border-black font-medium text-sm text-gray-900">
                        Users
                    </button>
                    <button onclick="switchTab('pending')" id="pendingTab" class="tab-btn py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                        Pending invites
                    </button>
                    <button onclick="switchTab('teams')" id="teamsTab" class="tab-btn py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                        Teams
                    </button>
                </nav>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative max-w-md">
                    <input type="text" id="searchInput" placeholder="Search" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Users Tab Content -->
            <div id="usersContent" class="tab-content">
                <div class="bg-white rounded-lg shadow-sm">
                    <!-- Table Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="grid grid-cols-12 gap-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="col-span-4">
                                Name <i class="fas fa-sort ml-1"></i>
                            </div>
                            <div class="col-span-3">Date added</div>
                            <div class="col-span-3">User role</div>
                            <div class="col-span-2"></div>
                        </div>
                    </div>

                    <!-- Table Body -->
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <div class="grid grid-cols-12 gap-4 items-center">
                                    <div class="col-span-4 flex items-center">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-span-3 text-sm text-gray-900">
                                        <?php echo date('j M Y', strtotime($user['created_at'])); ?>
                                    </div>
                                    <div class="col-span-3">
                                        <select class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option selected>Owner</option>
                                            <option>Admin</option>
                                            <option>Member</option>
                                            <option>Viewer</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2 text-right">
                                        <button onclick="showUserOptions(<?php echo $user['id']; ?>)" class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Pending Invites Tab Content -->
            <div id="pendingContent" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <i class="fas fa-envelope text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No pending invites</h3>
                    <p class="text-gray-500 mb-4">Invite team members to collaborate on legal documents</p>
                    <button onclick="openInviteModal()" class="text-blue-600 hover:text-blue-700 font-medium">
                        <i class="fas fa-plus mr-2"></i>Invite user
                    </button>
                </div>
            </div>

            <!-- Teams Tab Content -->
            <div id="teamsContent" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No teams created</h3>
                    <p class="text-gray-500 mb-4">Create teams to organize users and manage permissions</p>
                    <button onclick="openTeamModal()" class="text-blue-600 hover:text-blue-700 font-medium">
                        <i class="fas fa-plus mr-2"></i>Create team
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invite User Modal -->
<div id="inviteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-md w-full">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Invite User</h2>
                    <button onclick="closeInviteModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="inviteForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="inviteEmail" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter email address" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="inviteRole" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
                        <textarea id="inviteMessage" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Add a personal message..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeInviteModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-paper-plane mr-2"></i>Send Invite
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Create Team Modal -->
<div id="teamModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-md w-full">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Create Team</h2>
                    <button onclick="closeTeamModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="teamForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Team Name</label>
                        <input type="text" id="teamName" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter team name" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea id="teamDescription" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe the team's purpose..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeTeamModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-users mr-2"></i>Create Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-black', 'text-gray-900');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        document.getElementById(tabName + 'Content').classList.remove('hidden');
        
        // Add active class to selected tab
        const activeTab = document.getElementById(tabName + 'Tab');
        activeTab.classList.add('active', 'border-black', 'text-gray-900');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
    }

    function openInviteModal() {
        document.getElementById('inviteModal').classList.remove('hidden');
    }

    function closeInviteModal() {
        document.getElementById('inviteModal').classList.add('hidden');
        document.getElementById('inviteForm').reset();
    }

    function openTeamModal() {
        document.getElementById('teamModal').classList.remove('hidden');
    }

    function closeTeamModal() {
        document.getElementById('teamModal').classList.add('hidden');
        document.getElementById('teamForm').reset();
    }

    function showUserOptions(userId) {
        alert('User options for user ID: ' + userId + '\nThis would show options like: Edit, Remove, Change Role, etc.');
    }

    // Form submissions
    document.getElementById('inviteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('inviteEmail').value;
        const role = document.getElementById('inviteRole').value;
        const message = document.getElementById('inviteMessage').value;
        
        // Here you would typically send the invite via AJAX
        alert('Invite sent to: ' + email + ' with role: ' + role);
        closeInviteModal();
    });

    document.getElementById('teamForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('teamName').value;
        const description = document.getElementById('teamDescription').value;
        
        // Here you would typically create the team via AJAX
        alert('Team created: ' + name);
        closeTeamModal();
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        // Implement search logic here
        console.log('Searching for:', searchTerm);
    });
</script>