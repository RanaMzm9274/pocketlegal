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
                    <h1 class="text-2xl font-semibold text-gray-800">Knowledge Hub</h1>
                    <p class="text-gray-600 mt-1">Legal resources, guides, and best practices</p>
                </div>
                <div class="flex space-x-3">
                    <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-bookmark mr-2"></i>Bookmarks
                    </button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Resource
                    </button>
                </div>
            </div>
        </div>

        <!-- Knowledge Hub Content -->
        <div class="p-8">
            <!-- Quick Access -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl knowledge-card transition-all duration-300 cursor-pointer">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-gavel text-2xl mr-3"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Legal Guides</h3>
                    <p class="text-sm opacity-90">Comprehensive legal guides and tutorials</p>
                    <div class="mt-4 text-sm opacity-75">24 articles</div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl knowledge-card transition-all duration-300 cursor-pointer">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-book-open text-2xl mr-3"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Case Studies</h3>
                    <p class="text-sm opacity-90">Real-world legal case studies and analysis</p>
                    <div class="mt-4 text-sm opacity-75">18 cases</div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl knowledge-card transition-all duration-300 cursor-pointer">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-balance-scale text-2xl mr-3"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Regulations</h3>
                    <p class="text-sm opacity-90">Latest regulatory updates and compliance</p>
                    <div class="mt-4 text-sm opacity-75">32 updates</div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-xl knowledge-card transition-all duration-300 cursor-pointer">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-lightbulb text-2xl mr-3"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Best Practices</h3>
                    <p class="text-sm opacity-90">Industry best practices and tips</p>
                    <div class="mt-4 text-sm opacity-75">15 guides</div>
                </div>
            </div>

            <!-- Featured Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Featured Articles -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Featured Articles</h2>
                        <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</button>
                    </div>
                    
                    <div class="space-y-6">
                        <article class="flex space-x-4 p-4 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-contract text-blue-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">Contract Negotiation Strategies</h3>
                                <p class="text-gray-600 text-sm mb-2">Learn effective techniques for negotiating favorable contract terms and avoiding common pitfalls.</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <span>Legal Team</span>
                                    <span class="mx-2">•</span>
                                    <span>5 min read</span>
                                    <span class="mx-2">•</span>
                                    <span>Jan 20, 2024</span>
                                </div>
                            </div>
                        </article>

                        <article class="flex space-x-4 p-4 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                            <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">Data Privacy Compliance Guide</h3>
                                <p class="text-gray-600 text-sm mb-2">Comprehensive guide to GDPR, CCPA, and other data privacy regulations for businesses.</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <span>Compliance Team</span>
                                    <span class="mx-2">•</span>
                                    <span>12 min read</span>
                                    <span class="mx-2">•</span>
                                    <span>Jan 18, 2024</span>
                                </div>
                            </div>
                        </article>

                        <article class="flex space-x-4 p-4 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer">
                            <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-signature text-purple-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 mb-1">Electronic Signature Best Practices</h3>
                                <p class="text-gray-600 text-sm mb-2">Everything you need to know about implementing secure and legally binding e-signatures.</p>
                                <div class="flex items-center text-xs text-gray-500">
                                    <span>Technology Team</span>
                                    <span class="mx-2">•</span>
                                    <span>8 min read</span>
                                    <span class="mx-2">•</span>
                                    <span>Jan 15, 2024</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <!-- Quick Resources -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Quick Resources</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-download text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Contract Templates</h4>
                                <p class="text-xs text-gray-600">Download ready-to-use templates</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calculator text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Legal Calculators</h4>
                                <p class="text-xs text-gray-600">Calculate damages, interest, etc.</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Legal Calendar</h4>
                                <p class="text-xs text-gray-600">Important dates and deadlines</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-phone text-orange-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Expert Contacts</h4>
                                <p class="text-xs text-gray-600">Connect with legal experts</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Emergency Contacts</h4>
                                <p class="text-xs text-gray-600">24/7 legal emergency support</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Browse by Category</h2>
                    <div class="relative">
                        <input type="text" placeholder="Search knowledge base..." class="pl-8 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-2 top-3 text-gray-400 text-xs"></i>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-briefcase text-blue-600 text-xl mr-3"></i>
                            <h3 class="font-semibold text-gray-800">Corporate Law</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">Corporate governance, mergers, acquisitions, and business law.</p>
                        <div class="text-xs text-gray-500">42 articles</div>
                    </div>

                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-home text-green-600 text-xl mr-3"></i>
                            <h3 class="font-semibold text-gray-800">Real Estate</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">Property law, leases, purchases, and real estate transactions.</p>
                        <div class="text-xs text-gray-500">28 articles</div>
                    </div>

                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-users text-purple-600 text-xl mr-3"></i>
                            <h3 class="font-semibold text-gray-800">Employment Law</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">HR policies, employment contracts, and workplace regulations.</p>
                        <div class="text-xs text-gray-500">35 articles</div>
                    </div>

                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-globe text-orange-600 text-xl mr-3"></i>
                            <h3 class="font-semibold text-gray-800">International Law</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">Cross-border transactions, international trade, and treaties.</p>
                        <div class="text-xs text-gray-500">19 articles</div>
                    </div>

                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-laptop text-indigo-600 text-xl mr-3"></i>
                            <h3 class="font-semibold text-gray-800">Technology Law</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">Cybersecurity, data privacy, and technology regulations.</p>
                        <div class="text-xs text-gray-500">31 articles</div>
                    </div>

                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-heart text-red-600 text-xl mr-3"></i>
                            <h3 class="font-semibold text-gray-800">Healthcare Law</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">Medical regulations, HIPAA compliance, and healthcare policies.</p>
                        <div class="text-xs text-gray-500">24 articles</div>
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
</script>
