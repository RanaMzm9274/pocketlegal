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
                        <h1 class="text-2xl font-semibold text-gray-800">Contract Templates</h1>
                        <p class="text-gray-600 mt-1">Professional legal document templates</p>
                    </div>
                    <button onclick="showComingSoon('Create Template')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create Template
                    </button>
                </div>
            </div>

            <!-- Templates Content -->
            <div class="p-8">
                <!-- Template Categories -->
                <div class="flex flex-wrap gap-3 mb-8">
                    <button onclick="filterTemplates('all')" class="category-btn active px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white">All Templates</button>
                    <button onclick="filterTemplates('employment')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Employment</button>
                    <button onclick="filterTemplates('commercial')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Commercial</button>
                    <button onclick="filterTemplates('nda')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">NDAs</button>
                    <button onclick="filterTemplates('service')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Service Agreements</button>
                    <button onclick="filterTemplates('real-estate')" class="category-btn px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">Real Estate</button>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <input type="text" id="templateSearch" placeholder="Search templates..." class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                        </div>
                        <select class="border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Sort by: Most Popular</option>
                            <option>Sort by: Newest</option>
                            <option>Sort by: Name A-Z</option>
                            <option>Sort by: Category</option>
                        </select>
                    </div>
                </div>

                <!-- Templates Grid -->
                <div id="templatesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Templates will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Template Preview Modal -->
    <div id="templateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold" id="modalTitle">Template Preview</h2>
                        <button onclick="closeTemplateModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div id="templateContent">
                        <!-- Template content will be loaded here -->
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="closeTemplateModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Close</button>
                        <button onclick="showComingSoon('Use Template')" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-file-plus mr-2"></i>Use Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const templates = [
            {
                id: 1,
                title: "Employment Agreement",
                category: "employment",
                description: "Comprehensive employment contract template with standard terms and conditions.",
                tags: ["Full-time", "Benefits", "Termination"],
                popularity: 95,
                lastUpdated: "2024-01-15",
                icon: "fas fa-user-tie",
                color: "blue"
            },
            {
                id: 2,
                title: "Non-Disclosure Agreement",
                category: "nda",
                description: "Standard NDA template for protecting confidential information in business relationships.",
                tags: ["Confidentiality", "Trade Secrets", "Mutual"],
                popularity: 92,
                lastUpdated: "2024-01-10",
                icon: "fas fa-shield-alt",
                color: "green"
            },
            {
                id: 3,
                title: "Service Agreement",
                category: "service",
                description: "Professional services agreement template for consulting and service providers.",
                tags: ["Consulting", "Deliverables", "Payment"],
                popularity: 88,
                lastUpdated: "2024-01-12",
                icon: "fas fa-handshake",
                color: "purple"
            },
            {
                id: 4,
                title: "Commercial Lease Agreement",
                category: "real-estate",
                description: "Commercial property lease template with standard commercial terms.",
                tags: ["Lease", "Commercial", "Property"],
                popularity: 85,
                lastUpdated: "2024-01-08",
                icon: "fas fa-building",
                color: "orange"
            },
            {
                id: 5,
                title: "Independent Contractor Agreement",
                category: "employment",
                description: "Contract template for independent contractors and freelancers.",
                tags: ["Contractor", "Freelance", "1099"],
                popularity: 82,
                lastUpdated: "2024-01-14",
                icon: "fas fa-user-cog",
                color: "indigo"
            },
            {
                id: 6,
                title: "Software License Agreement",
                category: "commercial",
                description: "Software licensing template for SaaS and software products.",
                tags: ["Software", "License", "SaaS"],
                popularity: 78,
                lastUpdated: "2024-01-11",
                icon: "fas fa-code",
                color: "teal"
            },
            {
                id: 7,
                title: "Partnership Agreement",
                category: "commercial",
                description: "Business partnership agreement template for joint ventures.",
                tags: ["Partnership", "Joint Venture", "Equity"],
                popularity: 75,
                lastUpdated: "2024-01-09",
                icon: "fas fa-users",
                color: "red"
            },
            {
                id: 8,
                title: "Vendor Agreement",
                category: "commercial",
                description: "Vendor and supplier agreement template for procurement.",
                tags: ["Vendor", "Supplier", "Procurement"],
                popularity: 72,
                lastUpdated: "2024-01-13",
                icon: "fas fa-truck",
                color: "yellow"
            },
            {
                id: 9,
                title: "Consulting Agreement",
                category: "service",
                description: "Professional consulting services agreement template.",
                tags: ["Consulting", "Professional", "Hourly"],
                popularity: 70,
                lastUpdated: "2024-01-07",
                icon: "fas fa-chart-line",
                color: "pink"
            }
        ];

        let currentFilter = 'all';

        function toggleSubmenu(id) {
            const submenu = document.getElementById(id + '-submenu');
            submenu.classList.toggle('hidden');
        }

        function filterTemplates(category) {
            currentFilter = category;
            
            // Update category buttons
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('text-gray-600', 'hover:bg-gray-100');
            });
            
            event.target.classList.add('active', 'bg-blue-600', 'text-white');
            event.target.classList.remove('text-gray-600', 'hover:bg-gray-100');
            
            renderTemplates();
        }

        function renderTemplates() {
            const grid = document.getElementById('templatesGrid');
            const searchTerm = document.getElementById('templateSearch').value.toLowerCase();
            
            let filteredTemplates = templates;
            
            if (currentFilter !== 'all') {
                filteredTemplates = templates.filter(template => template.category === currentFilter);
            }
            
            if (searchTerm) {
                filteredTemplates = filteredTemplates.filter(template => 
                    template.title.toLowerCase().includes(searchTerm) ||
                    template.description.toLowerCase().includes(searchTerm) ||
                    template.tags.some(tag => tag.toLowerCase().includes(searchTerm))
                );
            }
            
            grid.innerHTML = '';
            
            if (filteredTemplates.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No templates found</h3>
                        <p class="text-gray-500">Try adjusting your search or filter criteria</p>
                    </div>
                `;
                return;
            }
            
            filteredTemplates.forEach(template => {
                const colorClasses = {
                    blue: 'bg-blue-100 text-blue-600',
                    green: 'bg-green-100 text-green-600',
                    purple: 'bg-purple-100 text-purple-600',
                    orange: 'bg-orange-100 text-orange-600',
                    indigo: 'bg-indigo-100 text-indigo-600',
                    teal: 'bg-teal-100 text-teal-600',
                    red: 'bg-red-100 text-red-600',
                    yellow: 'bg-yellow-100 text-yellow-600',
                    pink: 'bg-pink-100 text-pink-600'
                };
                
                const templateCard = document.createElement('div');
                templateCard.className = 'bg-white rounded-xl shadow-sm p-6 template-card transition-all duration-300 cursor-pointer';
                templateCard.onclick = () => openTemplateModal(template);
                
                templateCard.innerHTML = `
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 ${colorClasses[template.color]} rounded-lg flex items-center justify-center">
                            <i class="${template.icon} text-xl"></i>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center text-yellow-500">
                                <i class="fas fa-star text-xs"></i>
                                <span class="text-xs ml-1">${template.popularity}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">${template.title}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">${template.description}</p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        ${template.tags.map(tag => `
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">${tag}</span>
                        `).join('')}
                    </div>
                    
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Updated ${new Date(template.lastUpdated).toLocaleDateString()}</span>
                        <button onclick="event.stopPropagation(); showComingSoon('Use Template')" class="text-blue-600 hover:text-blue-700 font-medium">
                            Use Template
                        </button>
                    </div>
                `;
                
                grid.appendChild(templateCard);
            });
        }

        function openTemplateModal(template) {
            const modal = document.getElementById('templateModal');
            const title = document.getElementById('modalTitle');
            const content = document.getElementById('templateContent');
            
            title.textContent = template.title;
            
            content.innerHTML = `
                <div class="mb-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-16 h-16 bg-${template.color}-100 text-${template.color}-600 rounded-lg flex items-center justify-center">
                            <i class="${template.icon} text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">${template.title}</h3>
                            <p class="text-gray-600">${template.description}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Category</h4>
                            <p class="text-gray-600 capitalize">${template.category.replace('-', ' ')}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Popularity</h4>
                            <p class="text-gray-600">${template.popularity}% of users</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Last Updated</h4>
                            <p class="text-gray-600">${new Date(template.lastUpdated).toLocaleDateString()}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Tags</h4>
                            <div class="flex flex-wrap gap-1">
                                ${template.tags.map(tag => `
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">${tag}</span>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-medium text-gray-800 mb-3">Template Preview</h4>
                    <div class="bg-white border rounded p-4 text-sm text-gray-700 max-h-64 overflow-y-auto">
                        <p class="font-semibold mb-2">${template.title.toUpperCase()}</p>
                        <p class="mb-2">This ${template.title.toLowerCase()} ("Agreement") is entered into on [DATE] between [PARTY 1] and [PARTY 2].</p>
                        <p class="mb-2"><strong>1. SCOPE OF WORK</strong></p>
                        <p class="mb-2">[Description of services or work to be performed]</p>
                        <p class="mb-2"><strong>2. COMPENSATION</strong></p>
                        <p class="mb-2">[Payment terms and conditions]</p>
                        <p class="mb-2"><strong>3. TERM AND TERMINATION</strong></p>
                        <p class="mb-2">[Duration and termination conditions]</p>
                        <p class="text-gray-500 italic">... [Additional clauses and terms] ...</p>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeTemplateModal() {
            document.getElementById('templateModal').classList.add('hidden');
        }

        function showComingSoon(feature) {
            alert(feature + ' functionality will be implemented in the next phase.');
        }

        // Search functionality
        document.getElementById('templateSearch').addEventListener('input', function() {
            renderTemplates();
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            renderTemplates();
        });
    </script>