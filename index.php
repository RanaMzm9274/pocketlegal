<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// session_start();

include_once 'header.php';
require_once 'config/database.php';
require_once 'classes/Document.php';

$database = new Database();
$db = $database->getConnection();
$document = new Document($db);

$user_documents = $document->getUserDocuments($_SESSION['user_id']);
$recent_documents = array_slice($user_documents, 0, 5); // Get 5 most recent


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// ✅ Create all necessary tables and demo user
$database->createTables();

// include 'config.php';

// $sql = "SELECT * FROM users";
// $result = $conn->query($sql);

?>

<!-- Main Content -->
<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b px-8 py-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Welcome back <?php echo htmlspecialchars($user_name); ?>,</h1>
                <p class="text-gray-600 mt-1">Here is what's happening in your account</p>
            </div>
            <button class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Review Contract with AI -->
            <div class="gradient-blue text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openContractReviewModal()">
                <div class="flex items-center mb-4">
                    <i class="fas fa-robot text-2xl mr-3"></i>
                    <span class="text-sm opacity-90">AI</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Review contract with AI</h3>
                <p class="text-sm opacity-90">Get immediate responses to your questions and AI assistance with drafting and summarizing</p>
            </div>

            <!-- Create Document -->
            <div class="gradient-dark text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openCreateDocumentModal()">
                <div class="flex items-center mb-4">
                    <i class="fas fa-file-plus text-2xl mr-3"></i>
                    <span class="text-sm opacity-90">Template library</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Create a document</h3>
                <p class="text-sm opacity-90">Create a contract based on a template</p>
            </div>

            <!-- Upload Documents -->
            <div class="gradient-gray text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openUploadModal()">
                <div class="flex items-center mb-4">
                    <i class="fas fa-upload text-2xl mr-3"></i>
                    <span class="text-sm opacity-90">Repository</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Upload documents</h3>
                <p class="text-sm opacity-90">Upload files to the repository for storage and management</p>
            </div>

            <!-- Send for eSignature -->
            <div class="gradient-purple text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openESignatureModal()">
                <div class="flex items-center mb-4">
                    <i class="fas fa-signature text-2xl mr-3"></i>
                    <span class="text-sm opacity-90">eSigning</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Send for eSignature</h3>
                <p class="text-sm opacity-90">Upload a document and send for eSigning instantly</p>
            </div>
        </div>

        <!-- Additional Feature Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Legal Research -->
            <div class="gradient-green text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openLegalResearchModal()">
                <div class="flex items-center mb-4">
                    <i class="fas fa-search text-2xl mr-3"></i>
                    <span class="text-sm opacity-90">Research</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Legal Research</h3>
                <p class="text-sm opacity-90">Research legal precedents and case law</p>
            </div>

            <!-- Compliance Check -->
            <div class="gradient-orange text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="openComplianceModal()">
                <div class="flex items-center mb-4">
                    <i class="fas fa-shield-alt text-2xl mr-3"></i>
                    <span class="text-sm opacity-90">Compliance</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Compliance Check</h3>
                <p class="text-sm opacity-90">Verify regulatory compliance requirements</p>
            </div>

            <!-- Contract Analytics -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-6 rounded-xl card-hover transition-all duration-300 cursor-pointer" onclick="window.location.href='insights.php'">
                <div class="flex items-center mb-4">
                    <i class="fas fa-chart-pie text-2xl mr-3"></i>
                    <span class="text-sm opacity-90">Analytics</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Contract Analytics</h3>
                <p class="text-sm opacity-90">Analyze contract performance and risks</p>
            </div>
        </div>

        <!-- Contract Workflow and Tasks -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Contract Workflow -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Contract workflow</h2>
                    <div class="flex items-center space-x-4">
                        <select class="border rounded-lg px-3 py-1 text-sm">
                            <option>My documents</option>
                        </select>
                        <select class="border rounded-lg px-3 py-1 text-sm">
                            <option>Last 30 days</option>
                        </select>
                    </div>
                </div>

                <!-- Workflow Tabs -->
                <div class="flex space-x-6 border-b mb-6">
                    <button class="pb-2 text-sm font-medium text-gray-900 border-b-2 border-blue-500">All</button>
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Draft</button>
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Review</button>
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Agreed form</button>
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">eSigning</button>
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Signed</button>
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">Unknown</button>
                </div>

                <!-- Recent Documents -->
                <?php if (empty($recent_documents)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No documents</h3>
                        <p class="text-gray-500 mb-4">Upload your first document to get started.</p>
                        <button onclick="openUploadModal()" class="text-blue-600 hover:text-blue-700 font-medium">Upload Document</button>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recent_documents as $doc): ?>
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-file-alt text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($doc['original_name']); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($doc['created_at'])); ?></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $doc['status'] === 'uploaded' ? 'bg-green-100 text-green-800' : 
                                                    ($doc['status'] === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                        <?php echo ucfirst($doc['status']); ?>
                                    </span>
                                    <?php if ($doc['ai_processed']): ?>
                                        <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center pt-4">
                            <a href="documents.php" class="text-blue-600 hover:text-blue-700 font-medium text-sm">View all documents</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tasks -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Tasks</h2>
                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">Show all</button>
                </div>

                <!-- Task Tabs -->
                <div class="flex space-x-4 border-b mb-6">
                    <button class="pb-2 text-sm font-medium text-gray-500 hover:text-gray-700">To-do</button>
                    <button class="pb-2 text-sm font-medium text-gray-900 border-b-2 border-blue-500">Completed</button>
                </div>

                <!-- Completed State -->
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Well done!</h3>
                    <p class="text-gray-500">You have completed all your tasks</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contract Review Modal (Replaced with Juri AI Chat Interface) -->
<div id="contractReviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] flex flex-col font-inter">
            <!-- Header -->
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-robot text-blue-600 mr-2"></i>
                        Juri AI Assistant
                    </h2>
                    <div class="flex items-center space-x-2">
                        <button id="deleteConversationBtn" onclick="showDeleteConfirmation()" class="text-red-500 hover:text-red-700 hidden">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="closeContractReviewModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chat Interface -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Sidebar -->
                <div id="sidebar" class="w-64 bg-gray-50 border-r border-gray-200 flex flex-col">
                    <div class="p-4 border-b border-gray-100">
                        <button onclick="startNewChat()" class="flex items-center gap-3 w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-plus text-sm"></i>
                            <span class="font-medium">New Chat</span>
                        </button>
                    </div>
                    <div class="p-3 flex-1 overflow-y-auto">
                        <div id="chatList" class="space-y-2">
                            <!-- Chat items will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Chat Content -->
                <div class="flex-1 flex flex-col">
                    <!-- Welcome Screen -->
                    <div id="welcomeScreen" class="flex-1 flex items-center justify-center px-4 py-8 overflow-auto">
                        <div class="max-w-2xl w-full text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-robot text-white text-2xl"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-4">Hello! I'm Juri</h2>
                            <p class="text-gray-600 mb-8 text-lg">Your AI assistant powered by n8n. I can help you with UK legal queries and document processing.</p>
                            
                            <div class="grid md:grid-cols-2 gap-4 mb-8">
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer group" onclick="sendExampleMessage('What are the basic employment rights in the UK?')">
                                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-gavel"></i>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Legal Queries</h3>
                                    <p class="text-sm text-gray-600">Ask about UK laws, regulations, and legal procedures</p>
                                </div>
                                
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer group" onclick="document.getElementById('fileUpload').click()">
                                    <div class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Document Analysis</h3>
                                    <p class="text-sm text-gray-600">Upload PDF, DOCX, or RTF files for processing</p>
                                </div>
                            
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer group" onclick="sendExampleMessage('Explain the difference between contract law and tort law in the UK')">
                                    <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Legal Education</h3>
                                    <p class="text-sm text-gray-600">Learn about legal concepts and principles</p>
                                </div>
                                
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer group" onclick="sendExampleMessage('What should I include in a rental agreement?')">
                                    <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Property Law</h3>
                                    <p class="text-sm text-gray-600">Get guidance on property and tenancy matters</p>
                                </div>
                            </div>
                            
                            <div class="text-xs text-gray-500">
                                <p class="flex items-center justify-center gap-1">
                                    <span>💡</span>
                                    <strong>Tip:</strong>
                                    <span>For best results, be specific in your questions</span>
                                </p>
                                <p class="mt-1 flex items-center justify-center gap-2">
                                    <span>📄 Supported formats: PDF, DOCX, RTF</span>
                                    <span>•</span>
                                    <span>🔗 Powered by n8n</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div id="chatContainer" class="flex-1 hidden overflow-auto">
                        <div class="max-w-3xl mx-auto w-full px-4 pb-6">
                            <div id="chatMessages" class="py-6 space-y-6">
                                <!-- Messages will be rendered here -->
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Input Section -->
                    <div class="bg-white border-t border-gray-200 py-4 px-4">
                        <div class="max-w-3xl mx-auto w-full">
                            <div class="relative bg-gray-50 rounded-2xl border border-gray-200 flex items-end p-3 hover:border-gray-300 transition-colors focus-within:border-indigo-500 focus-within:bg-white">
                                <label class="absolute left-4 bottom-4 text-gray-400 hover:text-indigo-600 transition-colors cursor-pointer group">
                                    <i class="fas fa-paperclip text-lg"></i>
                                    <input type="file" id="fileUpload" accept=".pdf,.docx,.rtf" class="hidden">
                                    <span class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded-lg py-2 px-3 opacity-0 transition-opacity duration-300 pointer-events-none group-hover:opacity-100 whitespace-nowrap">
                                        Upload document
                                    </span>
                                </label>
                                <textarea 
                                    id="messageInput" 
                                    class="w-full px-12 py-3 bg-transparent resize-none outline-none min-h-[48px] max-h-[200px] text-base placeholder-gray-500" 
                                    placeholder="Message Juri..." 
                                    rows="3">
                                </textarea>
                                <button 
                                    id="sendButton" 
                                    onclick="sendMessage()" 
                                    class="ml-2 w-10 h-10 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl flex items-center justify-center hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg">
                                    <i class="fas fa-paper-plane text-sm"></i>
                                </button>
                            </div>
                            <div id="filePreview" class="mt-2 hidden">
                                <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-2 rounded-lg text-sm">
                                    <i class="fas fa-file"></i>
                                    <span id="fileName"></span>
                                    <button onclick="clearFile()" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <div class="text-xs text-gray-500">
                                    Press Shift+Enter for new line, Enter to send
                                </div>
                                <div class="text-xs text-gray-500">
                                    <span id="charCount">0</span>/2000 characters
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 opacity-0 invisible transition-all duration-300">
                <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4 transform translate-y-5 shadow-2xl" id="deleteModalContent">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-trash"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Delete Chat</h3>
                        <p class="text-gray-600 mb-6">Are you sure you want to delete this chat? This action cannot be undone.</p>
                        <div class="flex justify-center gap-3">
                            <button onclick="hideDeleteConfirmation()" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                                Cancel
                            </button>
                            <button onclick="deleteCurrentChat()" class="px-6 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Chat Modal -->
<div id="aiChatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-6xl w-full max-h-[90vh] flex">
            <!-- Conversations Sidebar -->
            <div class="w-80 border-r bg-gray-50 flex flex-col">
                <div class="p-4 border-b bg-white">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800">Conversations</h3>
                        <button onclick="startNewConversation()" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="relative">
                        <input type="text" id="conversationSearch" placeholder="Search conversations..." class="w-full pl-8 pr-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute left-2 top-3 text-gray-400 text-xs"></i>
                    </div>
                </div>
                <div id="conversationsList" class="flex-1 overflow-y-auto p-2">
                    <!-- Conversations will be loaded here -->
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="flex-1 flex flex-col">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-robot text-blue-600 mr-2"></i>
                            AI Contract Assistant
                        </h2>
                        <div class="flex items-center space-x-2">
                            <button id="deleteConversationBtn" onclick="deleteCurrentConversation()" class="text-red-500 hover:text-red-700 hidden">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick="closeAIChat()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Messages Area -->
                <div id="chatMessages" class="flex-1 p-6 overflow-y-auto bg-gray-50">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm max-w-2xl">
                                <p class="text-gray-800">Hello! I'm your AI contract assistant. Upload a contract and ask me anything about it - I can help with analysis, risk assessment, clause explanations, and more.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- File Upload Area -->
                <div class="p-4 border-t border-b bg-white">
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="file" id="chatFileInput" accept=".pdf,.doc,.docx,.txt" class="hidden" onchange="handleChatFileSelect(event)">
                            <button onclick="document.getElementById('chatFileInput').click()" class="flex items-center px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                                <i class="fas fa-paperclip mr-2"></i>
                                Attach Contract
                            </button>
                        </div>
                        <div id="chatFilePreview" class="flex items-center space-x-2"></div>
                    </div>
                </div>
                
                <!-- Chat Input Area -->
                <div class="p-6 bg-white">
                    <div class="flex space-x-3">
                        <div class="flex-1">
                            <textarea id="chatInput" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Ask me about your contract... (e.g., 'What are the key risks in this agreement?', 'Explain the termination clause', 'Is this contract favorable?')"></textarea>
                        </div>
                        <button onclick="sendChatMessage()" id="sendChatBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <div class="text-xs text-gray-500">
                            Press Shift+Enter for new line, Enter to send
                        </div>
                        <div class="text-xs text-gray-500">
                            <span id="charCount">0</span>/2000 characters
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl max-w-lg w-full">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Upload Documents</h2>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 mb-4">Drag and drop files here, or click to select</p>
                    <input type="file" id="fileInput" multiple class="hidden" onchange="handleFileSelect(event)">
                    <button onclick="document.getElementById('fileInput').click()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Select Files
                    </button>
                </div>
                <div id="fileList" class="mt-4 space-y-2"></div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeUploadModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button onclick="uploadFiles()" id="uploadBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50" disabled>
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Juri AI Chat Interface JavaScript
    let chats = JSON.parse(localStorage.getItem('juriChats') || '[]');
    let currentChat = null;
    let isWaitingForResponse = false;
    let pendingFile = null;

    // Webhook URLs - Configure these for your n8n setup
    const WEBHOOKS = {
        query: 'https://n8n.srv909751.hstgr.cloud/webhook/query',
        document: 'https://n8n.srv909751.hstgr.cloud/webhook/doc_upload'
    };

    async function makeRequest(url, options) {
        try {
            const response = await fetch(url, options);
            return response;
        } catch (error) {
            if (error.message.includes('CORS') || error.message.includes('Failed to fetch')) {
                console.warn('CORS error detected, trying alternative approach...');
                throw new Error('CORS_ERROR: Please configure CORS headers on your n8n webhook. Add these headers to your webhook response: Access-Control-Allow-Origin: *, Access-Control-Allow-Methods: POST, GET, OPTIONS, Access-Control-Allow-Headers: Content-Type');
            }
            throw error;
        }
    }

    function getErrorMessage(error, hasFile = false) {
        const errorMsg = error.message;
        
        if (errorMsg.includes('CORS_ERROR')) {
            return `**🔧 Configuration Required**

There's a CORS (Cross-Origin Resource Sharing) issue with your n8n webhook. To fix this, you need to configure CORS headers on your n8n webhook.

**Solution:**
1. In your n8n workflow, add a "Set" node before the webhook response
2. Add these headers:
   - \`Access-Control-Allow-Origin\`: \`*\`
   - \`Access-Control-Allow-Methods\`: \`POST, GET, OPTIONS\`
   - \`Access-Control-Allow-Headers\`: \`Content-Type\`

**Alternative:** Deploy this interface to the same domain as your n8n instance to avoid CORS issues.

**Technical Details:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('500')) {
            return `**⚠️ Server Error**

The server encountered an internal error while processing your request.

**Possible causes:**
- Issue processing the uploaded file
- n8n workflow configuration problem
- Server overload or timeout

**Solutions:**
- If you uploaded a file, ensure it's a valid PDF, DOCX, or RTF
- Try again in a few moments
- Check your n8n workflow logs for detailed error information

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('Empty') || errorMsg.includes('JSON')) {
            return `**📄 Response Format Issue**

The server returned an empty or invalid response.

**Common causes:**
- Your n8n workflow might not be returning any data
- The webhook endpoint might not be configured correctly
- The workflow might be failing silently

**Solutions:**
1. Check your n8n workflow execution logs
2. Ensure your workflow returns a JSON response
3. Test your webhook directly with a tool like Postman
4. Make sure the workflow is active and properly configured

**Technical Details:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('network') || errorMsg.includes('Failed to fetch')) {
            return `**🌐 Connection Error**

Unable to connect to the AI service.

**Possible causes:**
- Internet connection issues
- n8n webhook URL is incorrect or unreachable
- Server is temporarily unavailable

**Solutions:**
- Check your internet connection
- Verify the webhook URL is correct
- Try again in a few moments

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('404')) {
            return `**🔍 Webhook Not Found**

The webhook URL appears to be incorrect or the endpoint doesn't exist.

**Solutions:**
- Verify your n8n webhook URL is correct
- Ensure your n8n workflow is active
- Check that the webhook path matches your workflow configuration

**Error:** ${errorMsg}`;
        }
        
        return `**❌ Unexpected Error**

An unexpected error occurred while processing your request.

**Error Details:** ${errorMsg}

**What you can do:**
- Try refreshing the page and sending your message again
- If the problem persists, check the browser console for more details
- Contact support if the issue continues`;
    }

    // DOM elements for Juri AI
    const chatInterface = document.getElementById('contractReviewModal');
    const sidebar = document.getElementById('sidebar');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const fileUpload = document.getElementById('fileUpload');
    const deleteModal = document.getElementById('deleteModal');
    const deleteModalContent = document.getElementById('deleteModalContent');
    const welcomeScreen = document.getElementById('welcomeScreen');
    const chatContainer = document.getElementById('chatContainer');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');

    // Initialize Juri AI
    document.addEventListener('DOMContentLoaded', () => {
        setupJuriAIEventListeners();
        if (chats.length === 0 || currentChat === null) {
            startNewChat();
        } else {
            showChatContainer();
            renderMessages();
        }
        updateCharCount();
    });

    function setupJuriAIEventListeners() {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
            updateSendButton();
            updateCharCount();
        });

        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        fileUpload.addEventListener('change', function() {
            if (this.files.length > 0) {
                pendingFile = this.files[0];
                showFilePreview(pendingFile);
                updateSendButton();
            } else {
                clearFile();
            }
        });

        deleteModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteConfirmation();
            }
        });
    }

    function updateSendButton() {
        const hasText = messageInput.value.trim().length > 0;
        const hasFile = pendingFile !== null;
        sendButton.disabled = isWaitingForResponse || (!hasText && !hasFile);
        
        if (sendButton.disabled) {
            sendButton.classList.add('opacity-50', 'cursor-not-allowed');
            sendButton.classList.remove('hover:scale-105');
        } else {
            sendButton.classList.remove('opacity-50', 'cursor-not-allowed');
            sendButton.classList.add('hover:scale-105');
        }
    }

    function showFilePreview(file) {
        fileName.textContent = file.name;
        filePreview.classList.remove('hidden');
    }

    function clearFile() {
        pendingFile = null;
        fileUpload.value = '';
        filePreview.classList.add('hidden');
        updateSendButton();
    }

    function showWelcomeScreen() {
        welcomeScreen.classList.remove('hidden');
        chatContainer.classList.add('hidden');
    }

    function showChatContainer() {
        welcomeScreen.classList.add('hidden');
        chatContainer.classList.remove('hidden');
    }

    function createWelcomeMessage() {
        return {
            from: 'ai',
            text: `Hello! I'm Juri, your AI assistant powered by n8n. I can help you with:

**🏛️ UK Legal Queries**
- Employment rights and regulations
- Contract law and disputes
- Property and tenancy law
- Business and corporate law
- Consumer rights and protection

**📄 Document Processing**
- Legal document analysis
- Contract review and summary
- Policy interpretation
- Compliance checking

Feel free to ask me any legal question or upload a document for analysis. How can I assist you today?`,
            isWelcome: true,
            timestamp: new Date().toISOString()
        };
    }

    function saveChats() {
        localStorage.setItem('juriChats', JSON.stringify(chats));
    }

    function renderChats() {
        const list = document.getElementById('chatList');
        list.innerHTML = '';
        
        if (chats.length === 0) {
            list.innerHTML = '<div class="p-4 text-gray-500 text-center text-sm">No chats yet</div>';
            return;
        }
        
        chats.forEach((chat, index) => {
            const item = document.createElement('div');
            item.className = `group flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all duration-200 ${
                index === currentChat ? 'bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200' : 'hover:bg-gray-50'
            }`;
            
            item.innerHTML = `
                <div class="w-6 h-6 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-comment text-white text-xs"></i>
                </div>
                <span class="flex-1 truncate text-sm font-medium text-gray-900">${chat.title}</span>
                <button class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition-all duration-200" onclick="event.stopPropagation(); showDeleteConfirmation(${index})">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            `;
            
            item.onclick = () => switchChat(index);
            list.appendChild(item);
        });
    }

    function renderMessages() {
        const msgBox = document.getElementById('chatMessages');
        msgBox.innerHTML = '';
        
        if (currentChat === null || !chats[currentChat]) {
            return;
        }
        
        chats[currentChat].messages.forEach((msg, index) => {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex gap-4 animate-fade-in';
            
            if (msg.from === 'user') {
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 max-w-3xl">
                        <div class="text-gray-900">${msg.text}</div>
                    </div>
                `;
            } else {
                let formattedText = msg.text
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-sm font-mono">$1</code>')
                    .replace(/\n/g, '<br>');
                
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-green-500 to-teal-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-sm"></i>
                    </div>
                    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 max-w-3xl">
                        <div class="prose prose-sm max-w-none text-gray-900">${formattedText}</div>
                    </div>
                `;
            }
            
            msgBox.appendChild(messageDiv);
        });
        
        setTimeout(() => {
            msgBox.scrollTop = msgBox.scrollHeight;
        }, 100);
    }

    function startNewChat() {
        const title = `Chat ${chats.length + 1}`;
        const newChat = { 
            title, 
            messages: [createWelcomeMessage()],
            createdAt: new Date().toISOString()
        };
        
        chats.push(newChat);
        currentChat = chats.length - 1;
        
        saveChats();
        renderChats();
        showChatContainer();
        renderMessages();
        
        messageInput.focus();
        clearFile();
        document.getElementById('deleteConversationBtn').classList.remove('hidden');
    }

    function switchChat(index) {
        currentChat = index;
        renderChats();
        showChatContainer();
        renderMessages();
        
        messageInput.focus();
        document.getElementById('deleteConversationBtn').classList.remove('hidden');
    }

    function sendExampleMessage(message) {
        if (currentChat === null) {
            startNewChat();
        }
        messageInput.value = message;
        setTimeout(() => sendMessage(), 100);
    }

    function showDeleteConfirmation(index = null) {
        if (index !== null) {
            window.chatToDelete = index;
        } else {
            window.chatToDelete = currentChat;
        }
        
        if (window.chatToDelete === null || chats.length === 0) {
            return;
        }
        
        deleteModal.classList.remove('opacity-0', 'invisible');
        deleteModal.classList.add('opacity-100', 'visible');
        deleteModalContent.classList.remove('translate-y-5');
    }

    function hideDeleteConfirmation() {
        deleteModal.classList.remove('opacity-100', 'visible');
        deleteModal.classList.add('opacity-0', 'invisible');
        deleteModalContent.classList.add('translate-y-5');
        window.chatToDelete = null;
    }

    function deleteCurrentChat() {
        const indexToDelete = window.chatToDelete;
        if (indexToDelete === null || indexToDelete >= chats.length) {
            hideDeleteConfirmation();
            return;
        }
        
        chats.splice(indexToDelete, 1);
        
        if (currentChat === indexToDelete) {
            if (chats.length > 0) {
                currentChat = Math.min(currentChat, chats.length - 1);
                showChatContainer();
            } else {
                currentChat = null;
                showWelcomeScreen();
            }
        } else if (currentChat > indexToDelete) {
            currentChat--;
        }
        
        saveChats();
        renderChats();
        renderMessages();
        hideDeleteConfirmation();
        if (chats.length === 0) {
            document.getElementById('deleteConversationBtn').classList.add('hidden');
        }
    }

    async function sendMessage() {
        const text = messageInput.value.trim();
        
        if (!text && !pendingFile) {
            return;
        }
        
        if (currentChat === null) {
            startNewChat();
            setTimeout(() => sendMessage(), 100);
            return;
        }
        
        let displayText = text;
        
        if (pendingFile) {
            displayText = (text ? `${text}<br><br>` : '') + `📄 <strong>File:</strong> ${pendingFile.name}`;
        }
        
        chats[currentChat].messages.push({ 
            from: 'user', 
            text: displayText,
            timestamp: new Date().toISOString()
        });
        
        const userMessages = chats[currentChat].messages.filter(m => m.from === 'user');
        if (userMessages.length === 1 && text) {
            const title = text.length > 40 ? text.substring(0, 40) + '...' : text;
            chats[currentChat].title = title;
        }
        
        messageInput.value = '';
        messageInput.style.height = 'auto';
        const fileToSend = pendingFile;
        clearFile();
        
        saveChats();
        renderChats();
        renderMessages();
        showTypingIndicator();
        
        isWaitingForResponse = true;
        updateSendButton();
        
        try {
            const webhookUrl = fileToSend ? WEBHOOKS.document : WEBHOOKS.query;
            
            let requestOptions;
            
            if (fileToSend) {
                const formData = new FormData();
                if (text) formData.append('text', text);
                formData.append('data', fileToSend);
                
                requestOptions = {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                };
            } else {
                const payload = { text };
                
                requestOptions = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                    mode: 'cors'
                };
            }
            
            const response = await makeRequest(webhookUrl, requestOptions);
            
            if (!response.ok) {
                let errorDetails = `HTTP error! Status: ${response.status}`;
                try {
                    const errorText = await response.text();
                    errorDetails += `. Server response: ${errorText.substring(0, 200)}...`;
                } catch (e) {
                    console.warn('Could not parse error response:', e);
                }
                throw new Error(errorDetails);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                if (!textResponse.trim()) {
                    throw new Error('Empty response from server');
                }
                throw new Error(`Expected JSON response but got: ${contentType || 'unknown'}. Response: ${textResponse.substring(0, 200)}...`);
            }

            const responseText = await response.text();
            if (!responseText.trim()) {
                throw new Error('Empty JSON response from server');
            }

            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Response text:', responseText);
                throw new Error(`Invalid JSON response: ${responseText.substring(0, 200)}...`);
            }
            
            console.log('Webhook response:', JSON.stringify(data, null, 2));
            
            let aiResponse = '';
            if (data.data) {
                aiResponse = data.data;
            } else if (data.output) {
                aiResponse = data.output;
            } else if (data.text) {
                aiResponse = data.text;
            } else if (data.content) {
                aiResponse = `### Document Analysis: ${data.fileName || 'Uploaded File'}\n\n${data.content}`;
            } else if (Array.isArray(data) && data.length > 0 && data[0].text) {
                aiResponse = data[0].text;
            } else if (data.message) {
                aiResponse = data.message;
            } else {
                aiResponse = 'I received your request but the response format was unexpected. Please try again or contact support if the issue persists.';
                console.warn('Unexpected response format:', data);
            }
            
            chats[currentChat].messages.push({ 
                from: 'ai', 
                text: aiResponse,
                timestamp: new Date().toISOString(),
                webhookUsed: fileToSend ? 'document' : 'query'
            });
            
        } catch (error) {
            console.error('Error calling webhook:', error);
            
            const errorMessage = getErrorMessage(error, !!fileToSend);
            
            chats[currentChat].messages.push({ 
                from: 'ai', 
                text: errorMessage,
                timestamp: new Date().toISOString(),
                isError: true
            });
        } finally {
            hideTypingIndicator();
            saveChats();
            renderMessages();
            isWaitingForResponse = false;
            updateSendButton();
        }
    }

    function showTypingIndicator() {
        const msgBox = document.getElementById('chatMessages');
        const typingDiv = document.createElement('div');
        typingDiv.className = 'flex gap-4 animate-fade-in';
        typingDiv.id = 'typingIndicator';
        
        typingDiv.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-green-500 to-teal-500 text-white flex items-center justify-center flex-shrink-0">
                <i class="fas fa-robot text-sm"></i>
            </div>
            <div class="flex items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex gap-1">
                    <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 rounded-full bg-gray-400 animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
                <span class="ml-3 text-sm text-gray-500">Juri is thinking...</span>
            </div>
        `;
        
        msgBox.appendChild(typingDiv);
        msgBox.scrollTop = msgBox.scrollHeight;
    }

    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    function openContractReviewModal() {
        document.getElementById('contractReviewModal').classList.remove('hidden');
        if (chats.length === 0 || currentChat === null) {
            startNewChat();
        }
    }

    function closeContractReviewModal() {
        document.getElementById('contractReviewModal').classList.add('hidden');
    }

    // Existing JavaScript from original index.php
    let selectedFiles = [];
    let chatFile = null;
    let currentConversationId = null;
    let conversations = [];

    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    function openAIChat() {
        document.getElementById('aiChatModal').classList.remove('hidden');
        loadConversations();
        startNewConversation();
    }

    function closeAIChat() {
        document.getElementById('aiChatModal').classList.add('hidden');
        resetChatState();
    }
    
    function resetChatState() {
        currentConversationId = null;
        chatFile = null;
        document.getElementById('chatInput').value = '';
        document.getElementById('chatFilePreview').innerHTML = '';
        document.getElementById('deleteConversationBtn').classList.add('hidden');
        updateCharCount();
    }
    
    function startNewConversation() {
        resetChatState();
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm max-w-2xl">
                        <p class="text-gray-800">Hello! I'm your AI contract assistant. Upload a contract and ask me anything about it - I can help with analysis, risk assessment, clause explanations, and more.</p>
                    </div>
                </div>
            </div>
        `;
        
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'border-blue-200');
        });
    }
    
    function loadConversations() {
        $.ajax({
            url: 'api/get_conversations.php',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    conversations = response.conversations;
                    displayConversations();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading conversations:', error);
            }
        });
    }
    
    function displayConversations() {
        const conversationsList = document.getElementById('conversationsList');
        
        if (conversations.length === 0) {
            conversationsList.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-comments text-2xl mb-2"></i>
                    <p class="text-sm">No conversations yet</p>
                    <p class="text-xs">Start chatting to create your first conversation</p>
                </div>
            `;
            return;
        }
        
        conversationsList.innerHTML = conversations.map(conv => `
            <div class="conversation-item p-3 rounded-lg cursor-pointer hover:bg-gray-100 mb-2 border border-transparent" 
                 onclick="loadConversation(${conv.id})" data-conversation-id="${conv.id}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-sm truncate">${conv.title}</h4>
                        <p class="text-xs text-gray-500 truncate mt-1">${conv.last_message || 'No messages'}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-400">${conv.message_count} messages</span>
                            <span class="text-xs text-gray-400">${formatDate(conv.updated_at)}</span>
                        </div>
                    </div>
                    <button onclick="event.stopPropagation(); deleteConversation(${conv.id})" 
                            class="text-gray-400 hover:text-red-500 ml-2">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    function loadConversation(conversationId) {
        currentConversationId = conversationId;
        
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'border-blue-200');
        });
        
        const activeItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
        if (activeItem) {
            activeItem.classList.add('bg-blue-50', 'border-blue-200');
        }
        
        document.getElementById('deleteConversationBtn').classList.remove('hidden');
        
        $.ajax({
            url: `api/get_conversations.php?conversation_id=${conversationId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayConversationMessages(response.messages);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading conversation:', error);
            }
        });
    }
    
    function displayConversationMessages(messages) {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.innerHTML = '<div class="space-y-4"></div>';
        const container = chatMessages.querySelector('.space-y-4');
        
        messages.forEach(message => {
            addMessageToChat(message.message_type, message.content, message.document_name, false);
        });
    }
    
    function deleteConversation(conversationId) {
        if (!confirm('Are you sure you want to delete this conversation? This action cannot be undone.')) {
            return;
        }
        
        $.ajax({
            url: 'api/delete_conversation.php',
            method: 'POST',
            data: JSON.stringify({ conversation_id: conversationId }),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    loadConversations();
                    if (currentConversationId === conversationId) {
                        startNewConversation();
                    }
                } else {
                    alert('Error deleting conversation: ' + response.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Error deleting conversation: ' + error);
            }
        });
    }
    
    function deleteCurrentConversation() {
        if (currentConversationId) {
            deleteConversation(currentConversationId);
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) {
            return 'Today';
        } else if (diffDays === 2) {
            return 'Yesterday';
        } else if (diffDays <= 7) {
            return `${diffDays - 1} days ago`;
        } else {
            return date.toLocaleDateString();
        }
    }

    function showComingSoon(feature) {
        alert(feature + ' functionality will be implemented in the next phase.');
    }

    function handleChatFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            chatFile = file;
            const preview = document.getElementById('chatFilePreview');
            preview.innerHTML = `
                <div class="flex items-center space-x-2 bg-blue-50 px-3 py-1 rounded-lg">
                    <i class="fas fa-file text-blue-600"></i>
                    <span class="text-sm text-blue-800">${file.name}</span>
                    <button onclick="removeChatFile()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            `;
        }
    }

    function removeChatFile() {
        chatFile = null;
        document.getElementById('chatFilePreview').innerHTML = '';
        document.getElementById('chatFileInput').value = '';
    }

    function sendChatMessage() {
        const input = document.getElementById('chatInput');
        const query = input.value.trim();
        
        if (!query) {
            alert('Please enter a message');
            return;
        }

        addMessageToChat('user', query, chatFile ? chatFile.name : null);
        
        input.value = '';
        updateCharCount();
        
        addTypingIndicator();
        
        const formData = new FormData();
        formData.append('query', query);
        
        if (currentConversationId) {
            formData.append('conversation_id', currentConversationId);
        }
        
        if (chatFile) {
            formData.append('document', chatFile);
        }
        
        $.ajax({
            url: 'api/ai_contract_review.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                removeTypingIndicator();
                if (response.success) {
                    if (!currentConversationId && response.conversation_id) {
                        currentConversationId = response.conversation_id;
                        document.getElementById('deleteConversationBtn').classList.remove('hidden');
                        loadConversations();
                    }
                    
                    addMessageToChat('assistant', response.response);
                    
                    if (response.webhook_success) {
                        console.log('✅ Webhook response received successfully');
                    } else {
                        console.log('⚠️ AI analysis service unavailable');
                    }
                } else {
                    addMessageToChat('assistant', 'I apologize, but I encountered an error processing your request. Please try again.');
                }
                removeChatFile();
            },
            error: function(xhr, status, error) {
                removeTypingIndicator();
                addMessageToChat('assistant', 'I apologize, but I encountered an error processing your request. Please try again.');
                console.error('AI Query Error:', error);
            }
        });
    }

    function addMessageToChat(sender, message, documentName = null, animate = true) {
        const chatMessages = document.getElementById('chatMessages');
        const container = chatMessages.querySelector('.space-y-4') || chatMessages;
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex items-start space-x-3 ${animate ? 'animate-fade-in' : ''}`;
        
        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white text-sm">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <div class="bg-blue-600 text-white p-4 rounded-lg shadow-sm max-w-2xl">
                    ${documentName ? `<div class="mb-2 text-blue-100 text-sm"><i class="fas fa-paperclip mr-1"></i>${documentName}</div>` : ''}
                    <p class="whitespace-pre-wrap">${message}</p>
                </div>
            `;
        } else if (sender === 'assistant') {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm max-w-2xl">
                    <p class="text-gray-800 whitespace-pre-wrap">${message}</p>
                </div>
            `;
        }
        
        container.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function addTypingIndicator() {
        const chatMessages = document.getElementById('chatMessages');
        const container = chatMessages.querySelector('.space-y-4') || chatMessages;
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'flex items-start space-x-3';
        typingDiv.innerHTML = `
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                <i class="fas fa-robot"></i>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        `;
        container.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    function updateCharCount() {
        const input = document.getElementById('chatInput');
        const charCount = document.getElementById('charCount');
        charCount.textContent = input.value.length;

        // Update Juri AI message input character count
        const juriInput = document.getElementById('messageInput');
        if (juriInput) {
            const juriCharCount = document.getElementById('charCount');
            juriCharCount.textContent = juriInput.value.length;
        }
    }

    document.getElementById('chatInput').addEventListener('input', updateCharCount);
    document.getElementById('chatInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendChatMessage();
        }
    });

    function handleFileSelect(event) {
        const files = Array.from(event.target.files);
        selectedFiles = files;
        displayFileList();
        document.getElementById('uploadBtn').disabled = files.length === 0;
    }

    function displayFileList() {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
            fileItem.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file mr-2 text-gray-500"></i>
                    <span class="text-sm">${file.name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024).toFixed(1)} KB)</span>
                </div>
                <button onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        displayFileList();
        document.getElementById('uploadBtn').disabled = selectedFiles.length === 0;
    }

    function uploadFiles() {
        if (selectedFiles.length === 0) return;

        const formData = new FormData();
        selectedFiles.forEach((file, index) => {
            formData.append('documents[]', file);
        });

        const uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        uploadBtn.disabled = true;

        $.ajax({
            url: 'api/upload.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Documents uploaded successfully!');
                closeUploadModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr, status, error) {
                alert('Error uploading documents: ' + error);
            },
            complete: function() {
                uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload';
                uploadBtn.disabled = false;
            }
        });
    }

    function openCreateDocumentModal() {
        alert('Create Document functionality: Choose from professional templates, customize with AI assistance, and generate contracts instantly.');
    }

    function openESignatureModal() {
        alert('eSignature functionality: Upload documents, add signature fields, send to multiple parties, and track signing progress in real-time.');
    }

    function openLegalResearchModal() {
        alert('Legal Research functionality: Search case law, statutes, regulations, and legal precedents with AI-powered analysis.');
    }

    function openComplianceModal() {
        alert('Compliance Check functionality: Verify documents against regulatory requirements, industry standards, and legal compliance frameworks.');
    }

    function openUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        selectedFiles = [];
        document.getElementById('fileList').innerHTML = '';
        document.getElementById('uploadBtn').disabled = true;
    }

    const uploadArea = document.querySelector('#uploadModal .border-dashed');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        uploadArea.classList.add('border-blue-500', 'bg-blue-50');
    }

    function unhighlight(e) {
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
    }

    uploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        selectedFiles = Array.from(files);
        displayFileList();
        document.getElementById('uploadBtn').disabled = files.length === 0;
    }

    const style = document.createElement('style');
    style.textContent = `
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        .conversation-item:hover {
            background-color: #f9fafb;
        }
        .conversation-item.active {
            background-color: #eff6ff;
            border-color: #dbeafe;
        }
        /* Juri AI Styles */
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        .animate-bounce {
            animation: bounce 1.4s infinite ease-in-out;
        }
        @keyframes bounce {
            0%, 60%, 100% { 
                transform: translateY(0); 
            }
            30% { 
                transform: translateY(-8px); 
            }
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        /* Enhanced prose styles */
        .prose {
            color: #374151;
            line-height: 1.6;
        }
        .prose h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
            margin-top: 1.5rem;
            color: #111827;
        }
        .prose h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            color: #111827;
        }
        .prose h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: #111827;
        }
        .prose p {
            margin-bottom: 1rem;
        }
        .prose ul, .prose ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
            padding-left: 0;
        }
        .prose li {
            margin-bottom: 0.5rem;
        }
        .prose code {
            background-color: #f3f4f6;
            color: #374151;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-family: 'SF Mono', Monaco, 'Inconsolata', 'Roboto Mono', monospace;
        }
        .prose pre {
            background-color: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .prose pre code {
            background-color: transparent;
            color: inherit;
            padding: 0;
        }
        .prose blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1rem;
            margin: 1rem 0;
            font-style: italic;
            color: #6b7280;
        }
        .prose strong {
            font-weight: 600;
            color: #111827;
        }
        /* Smooth transitions for all interactive elements */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
        /* Input focus styles */
        #messageInput:focus {
            outline: none;
        }
        /* Button press effect */
        button:active {
            transform: scale(0.98);
        }
    `;
    document.head.appendChild(style);
</script>

</div>

