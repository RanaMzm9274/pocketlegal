<?php


$user_name = $_SESSION['user_name'] ?? 'User';
include_once 'header.php';
?>


        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm border-b px-8 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Legal Insights</h1>
                        <p class="text-gray-600 mt-1">Analytics and insights for your legal operations</p>
                    </div>
                    <div class="flex space-x-3">
                        <select class="border rounded-lg px-3 py-2 text-sm">
                            <option>Last 30 days</option>
                            <option>Last 90 days</option>
                            <option>Last year</option>
                        </select>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>Export Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Insights Content -->
            <div class="p-8">
                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-sm insight-card transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-contract text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-semibold text-gray-800">24</p>
                                <p class="text-gray-600 text-sm">Active Contracts</p>
                                <p class="text-green-600 text-xs mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i>12% from last month
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm insight-card transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-semibold text-gray-800">$2.4M</p>
                                <p class="text-gray-600 text-sm">Contract Value</p>
                                <p class="text-green-600 text-xs mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i>8% from last month
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm insight-card transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-semibold text-gray-800">5.2</p>
                                <p class="text-gray-600 text-sm">Avg Review Days</p>
                                <p class="text-red-600 text-xs mt-1">
                                    <i class="fas fa-arrow-down mr-1"></i>15% from last month
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm insight-card transition-all duration-300">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-semibold text-gray-800">3</p>
                                <p class="text-gray-600 text-sm">Risk Alerts</p>
                                <p class="text-yellow-600 text-xs mt-1">
                                    <i class="fas fa-minus mr-1"></i>Same as last month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Contract Status Chart -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Contract Status Distribution</h3>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                        <div class="relative h-64">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>

                    <!-- Monthly Trends Chart -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Monthly Contract Trends</h3>
                            <button class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                        <div class="relative h-64">
                            <canvas id="trendsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Risk Analysis & Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Risk Analysis -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Risk Analysis</h3>
                            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4 p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">High Risk Contract Detected</h4>
                                    <p class="text-sm text-gray-600 mt-1">TechCorp NDA contains unusual liability clauses that may expose significant risk.</p>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-xs text-gray-500">Contract: TechCorp_NDA_2024.pdf</span>
                                        <button class="text-xs text-blue-600 hover:text-blue-700">Review Now</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4 p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">Contract Renewal Due</h4>
                                    <p class="text-sm text-gray-600 mt-1">Service agreement with DataFlow expires in 15 days.</p>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-xs text-gray-500">Due: Feb 10, 2024</span>
                                        <button class="text-xs text-blue-600 hover:text-blue-700">Set Reminder</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">Compliance Check Required</h4>
                                    <p class="text-sm text-gray-600 mt-1">Quarterly compliance review needed for 5 active contracts.</p>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-xs text-gray-500">Due: Jan 31, 2024</span>
                                        <button class="text-xs text-blue-600 hover:text-blue-700">Schedule Review</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
                            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Contract Signed</p>
                                    <p class="text-xs text-gray-600">Employment_Agreement_John_Doe.pdf</p>
                                    <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-upload text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Document Uploaded</p>
                                    <p class="text-xs text-gray-600">Vendor_Agreement_ABC_Corp.pdf</p>
                                    <p class="text-xs text-gray-500 mt-1">5 hours ago</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-robot text-purple-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">AI Analysis Complete</p>
                                    <p class="text-xs text-gray-600">Risk assessment for NDA_TechStart.pdf</p>
                                    <p class="text-xs text-gray-500 mt-1">1 day ago</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-edit text-yellow-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Contract Modified</p>
                                    <p class="text-xs text-gray-600">Service_Agreement_DataFlow.pdf</p>
                                    <p class="text-xs text-gray-500 mt-1">2 days ago</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-exclamation text-red-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Risk Alert</p>
                                    <p class="text-xs text-gray-600">High-risk clause detected in new contract</p>
                                    <p class="text-xs text-gray-500 mt-1">3 days ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id + '-submenu');
            submenu.classList.toggle('hidden');
        }

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Load Chart.js
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = function() {
                initializeCharts();
            };
            document.head.appendChild(script);
        });
        
        function initializeCharts() {
            // Contract Status Pie Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Draft', 'Review', 'Agreed Form', 'eSigning', 'Signed'],
                    datasets: [{
                        data: [5, 8, 3, 4, 12],
                        backgroundColor: [
                            '#FEF3C7',
                            '#DBEAFE',
                            '#D1FAE5',
                            '#E0E7FF',
                            '#10B981'
                        ],
                        borderColor: [
                            '#F59E0B',
                            '#3B82F6',
                            '#10B981',
                            '#6366F1',
                            '#059669'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            // Monthly Trends Line Chart
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                    datasets: [{
                        label: 'New Contracts',
                        data: [12, 19, 15, 25, 22, 18, 24],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Completed',
                        data: [8, 15, 12, 20, 18, 15, 20],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    </script>
