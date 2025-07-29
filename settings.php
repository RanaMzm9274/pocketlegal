<?php

include_once 'header.php';

$user_name = $_SESSION['user_name'] ?? 'User';
$user_email = $_SESSION['user_email'] ?? 'user@example.com';
?>


        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm border-b px-8 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Account Settings</h1>
                        <p class="text-gray-600 mt-1">Manage your account preferences and security</p>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="p-8">
                <div class="max-w-4xl mx-auto">
                    <!-- Profile Settings -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8 setting-card transition-all duration-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-6">Profile Information</h2>
                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" value="<?php echo htmlspecialchars($user_name); ?>" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" value="<?php echo htmlspecialchars($user_email); ?>" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" placeholder="+1 (555) 123-4567" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Organization</label>
                                    <input type="text" placeholder="MT London" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Settings -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8 setting-card transition-all duration-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-6">Security & Privacy</h2>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-800">Two-Factor Authentication</h3>
                                    <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                                </div>
                                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-shield-alt mr-2"></i>Enable
                                </button>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-800">Change Password</h3>
                                    <p class="text-sm text-gray-600">Update your account password</p>
                                </div>
                                <button onclick="openPasswordModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-key mr-2"></i>Change
                                </button>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-800">Login Sessions</h3>
                                    <p class="text-sm text-gray-600">Manage your active login sessions</p>
                                </div>
                                <button class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-list mr-2"></i>View Sessions
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8 setting-card transition-all duration-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-6">Notifications</h2>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-800">Email Notifications</h3>
                                    <p class="text-sm text-gray-600">Receive updates about your contracts and tasks</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-800">Push Notifications</h3>
                                    <p class="text-sm text-gray-600">Get notified about urgent matters</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-800">Weekly Reports</h3>
                                    <p class="text-sm text-gray-600">Receive weekly activity summaries</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Billing & Subscription -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-8 setting-card transition-all duration-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-6">Billing & Subscription</h2>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div>
                                    <h3 class="font-medium text-blue-800">Professional Plan</h3>
                                    <p class="text-sm text-blue-600">$49/month • Next billing: Feb 15, 2024</p>
                                </div>
                                <div class="flex space-x-3">
                                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-credit-card mr-2"></i>Update Payment
                                    </button>
                                    <button class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-arrow-up mr-2"></i>Upgrade
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center p-4 border rounded-lg">
                                    <div class="text-2xl font-semibold text-gray-800">156</div>
                                    <div class="text-sm text-gray-600">Documents Processed</div>
                                </div>
                                <div class="text-center p-4 border rounded-lg">
                                    <div class="text-2xl font-semibold text-gray-800">2.4GB</div>
                                    <div class="text-sm text-gray-600">Storage Used</div>
                                </div>
                                <div class="text-center p-4 border rounded-lg">
                                    <div class="text-2xl font-semibold text-gray-800">89</div>
                                    <div class="text-sm text-gray-600">AI Queries</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-red-200 setting-card transition-all duration-200">
                        <h2 class="text-lg font-semibold text-red-800 mb-6">Danger Zone</h2>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-red-800">Export Account Data</h3>
                                    <p class="text-sm text-red-600">Download all your data before deletion</p>
                                </div>
                                <button class="px-4 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                    <i class="fas fa-download mr-2"></i>Export Data
                                </button>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-red-800">Delete Account</h3>
                                    <p class="text-sm text-red-600">Permanently delete your account and all data</p>
                                </div>
                                <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl max-w-md w-full">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold">Change Password</h2>
                        <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closePasswordModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Update Password
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

        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }

        function confirmDelete() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                alert('Account deletion functionality would be implemented here.');
            }
        }
    </script>
