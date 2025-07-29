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

$database->createTables();
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
                    <a href="tasks.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Show all</a>
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

<!-- Contract Review Modal (Juri AI Chat Interface) -->
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
                            <div class="text-xs text-gray-500" id="editingStatus" style="display: none;">
                                <span class="text-blue-600">✏️ Editing message - Press Enter to save, Escape to cancel</span>
                            </div>
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