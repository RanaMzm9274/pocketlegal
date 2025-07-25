<?php

include_once 'header.php';
$user_name = $_SESSION['user_name'] ?? 'User';
?>


    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Tasks</h1>
                    <p class="text-gray-600 mt-1">Manage your legal tasks and deadlines</p>
                </div>
                <button onclick="openTaskModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>New Task
                </button>
            </div>
        </div>

        <!-- Tasks Content -->
        <div class="p-8">
            <!-- Task Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800" id="totalTasks">5</p>
                            <p class="text-gray-600 text-sm">Total Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800" id="pendingTasks">3</p>
                            <p class="text-gray-600 text-sm">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800" id="completedTasks">2</p>
                            <p class="text-gray-600 text-sm">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800" id="overdueTasks">1</p>
                            <p class="text-gray-600 text-sm">Overdue</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Filters -->
            <div class="bg-white rounded-xl shadow-sm mb-6">
                <div class="p-6 border-b">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex space-x-4">
                            <button onclick="filterTasks('all')" class="filter-btn active px-4 py-2 rounded-lg text-sm font-medium">All Tasks</button>
                            <button onclick="filterTasks('todo')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium">To Do</button>
                            <button onclick="filterTasks('in_progress')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium">In Progress</button>
                            <button onclick="filterTasks('completed')" class="filter-btn px-4 py-2 rounded-lg text-sm font-medium">Completed</button>
                        </div>
                        <div class="flex space-x-3">
                            <select class="border rounded-lg px-3 py-2 text-sm">
                                <option>All Priorities</option>
                                <option>High</option>
                                <option>Medium</option>
                                <option>Low</option>
                            </select>
                            <div class="relative">
                                <input type="text" placeholder="Search tasks..." class="pl-8 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search absolute left-2 top-3 text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Grid -->
            <div id="tasksContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Sample tasks will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-md w-full">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold" id="modalTitle">New Task</h2>
                    <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="taskForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="taskTitle" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter task title">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="taskDescription" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter task description"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <select id="taskPriority" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input type="date" id="taskDueDate" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeTaskModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Save Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let tasks = [
        {
            id: 1,
            title: "Review NDA with TechCorp",
            description: "Review and analyze the non-disclosure agreement with TechCorp for potential risks and compliance issues.",
            status: "todo",
            priority: "high",
            dueDate: "2024-01-25",
            createdAt: "2024-01-20"
        },
        {
            id: 2,
            title: "Draft Employment Contract",
            description: "Create employment contract template for new hires in the engineering department.",
            status: "in_progress",
            priority: "medium",
            dueDate: "2024-01-28",
            createdAt: "2024-01-18"
        },
        {
            id: 3,
            title: "Legal Compliance Audit",
            description: "Conduct quarterly legal compliance audit for all active contracts and agreements.",
            status: "completed",
            priority: "high",
            dueDate: "2024-01-22",
            createdAt: "2024-01-15"
        },
        {
            id: 4,
            title: "Update Privacy Policy",
            description: "Update company privacy policy to comply with latest GDPR requirements.",
            status: "todo",
            priority: "medium",
            dueDate: "2024-01-30",
            createdAt: "2024-01-19"
        },
        {
            id: 5,
            title: "Contract Renewal Reminder",
            description: "Set up reminders for upcoming contract renewals in Q2 2024.",
            status: "completed",
            priority: "low",
            dueDate: "2024-01-20",
            createdAt: "2024-01-16"
        }
    ];

    let currentFilter = 'all';

    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    function openTaskModal(taskId = null) {
        const modal = document.getElementById('taskModal');
        const title = document.getElementById('modalTitle');
        
        if (taskId) {
            const task = tasks.find(t => t.id === taskId);
            title.textContent = 'Edit Task';
            document.getElementById('taskTitle').value = task.title;
            document.getElementById('taskDescription').value = task.description;
            document.getElementById('taskPriority').value = task.priority;
            document.getElementById('taskDueDate').value = task.dueDate;
        } else {
            title.textContent = 'New Task';
            document.getElementById('taskForm').reset();
        }
        
        modal.classList.remove('hidden');
    }

    function closeTaskModal() {
        document.getElementById('taskModal').classList.add('hidden');
        document.getElementById('taskForm').reset();
    }

    function filterTasks(status) {
        currentFilter = status;
        
        // Update filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        
        event.target.classList.add('active', 'bg-blue-600', 'text-white');
        event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
        
        renderTasks();
    }

    function renderTasks() {
        const container = document.getElementById('tasksContainer');
        const filteredTasks = currentFilter === 'all' ? tasks : tasks.filter(task => task.status === currentFilter);
        
        container.innerHTML = '';
        
        if (filteredTasks.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks found</h3>
                    <p class="text-gray-500 mb-4">Create your first task to get started</p>
                    <button onclick="openTaskModal()" class="text-blue-600 hover:text-blue-700 font-medium">
                        <i class="fas fa-plus mr-2"></i>New Task
                    </button>
                </div>
            `;
            return;
        }
        
        filteredTasks.forEach(task => {
            const isOverdue = new Date(task.dueDate) < new Date() && task.status !== 'completed';
            const priorityColors = {
                high: 'bg-red-100 text-red-800',
                medium: 'bg-yellow-100 text-yellow-800',
                low: 'bg-green-100 text-green-800'
            };
            
            const statusColors = {
                todo: 'bg-gray-100 text-gray-800',
                in_progress: 'bg-blue-100 text-blue-800',
                completed: 'bg-green-100 text-green-800'
            };
            
            const taskCard = document.createElement('div');
            taskCard.className = 'bg-white rounded-xl shadow-sm p-6 task-card transition-all duration-200';
            taskCard.innerHTML = `
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${priorityColors[task.priority]}">
                            ${task.priority.toUpperCase()}
                        </span>
                        ${isOverdue ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">OVERDUE</span>' : ''}
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="openTaskModal(${task.id})" class="text-gray-400 hover:text-blue-600">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteTask(${task.id})" class="text-gray-400 hover:text-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <h3 class="text-lg font-semibold text-gray-800 mb-2">${task.title}</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">${task.description}</p>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusColors[task.status]}">
                            ${task.status.replace('_', ' ').toUpperCase()}
                        </span>
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>
                            ${new Date(task.dueDate).toLocaleDateString()}
                        </span>
                    </div>
                    <button onclick="toggleTaskStatus(${task.id})" class="text-sm font-medium ${task.status === 'completed' ? 'text-green-600' : 'text-blue-600'} hover:underline">
                        ${task.status === 'completed' ? 'Completed' : 'Mark Complete'}
                    </button>
                </div>
            `;
            
            container.appendChild(taskCard);
        });
    }

    function toggleTaskStatus(taskId) {
        const task = tasks.find(t => t.id === taskId);
        if (task.status === 'completed') {
            task.status = 'todo';
        } else {
            task.status = 'completed';
        }
        updateTaskStats();
        renderTasks();
    }

    function deleteTask(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            tasks = tasks.filter(t => t.id !== taskId);
            updateTaskStats();
            renderTasks();
        }
    }

    function updateTaskStats() {
        const total = tasks.length;
        const completed = tasks.filter(t => t.status === 'completed').length;
        const pending = tasks.filter(t => t.status !== 'completed').length;
        const overdue = tasks.filter(t => new Date(t.dueDate) < new Date() && t.status !== 'completed').length;
        
        document.getElementById('totalTasks').textContent = total;
        document.getElementById('completedTasks').textContent = completed;
        document.getElementById('pendingTasks').textContent = pending;
        document.getElementById('overdueTasks').textContent = overdue;
    }

    // Form submission
    document.getElementById('taskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const title = document.getElementById('taskTitle').value;
        const description = document.getElementById('taskDescription').value;
        const priority = document.getElementById('taskPriority').value;
        const dueDate = document.getElementById('taskDueDate').value;
        
        if (!title || !description || !dueDate) {
            alert('Please fill in all required fields');
            return;
        }
        
        const newTask = {
            id: Date.now(),
            title,
            description,
            status: 'todo',
            priority,
            dueDate,
            createdAt: new Date().toISOString().split('T')[0]
        };
        
        tasks.unshift(newTask);
        updateTaskStats();
        renderTasks();
        closeTaskModal();
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateTaskStats();
        renderTasks();
        
        // Set active filter button
        document.querySelector('.filter-btn.active').classList.add('bg-blue-600', 'text-white');
    });
</script>
