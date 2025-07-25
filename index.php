<?php
include_once 'header.php';
require_once 'config/database.php';
require_once 'classes/Document.php';

$database = new Database();
$db = $database->getConnection();
$document = new Document($db);

// Get user documents and stats
$user_documents = $document->getUserDocuments($_SESSION['user_id']);
$total_documents = count($user_documents);
$processed_documents = count(array_filter($user_documents, function($doc) { return $doc['ai_processed']; }));
$pending_documents = $total_documents - $processed_documents;

// Get AI conversations count
$conv_query = "SELECT COUNT(*) as count FROM ai_conversations WHERE user_id = ?";
$conv_stmt = $db->prepare($conv_query);
$conv_stmt->execute([$_SESSION['user_id']]);
$conversations_count = $conv_stmt->fetch(PDO::FETCH_ASSOC)['count'];

$user_name = $_SESSION['user_name'] ?? 'User';
?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
                    <p class="text-gray-600 mt-1">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openAIModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-robot mr-2"></i>AI Assistant
                    </button>
                    <button onclick="openUploadModal()" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Upload Document
                    </button>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo $total_documents; ?></p>
                            <p class="text-gray-600 text-sm">Total Documents</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo $processed_documents; ?></p>
                            <p class="text-gray-600 text-sm">AI Processed</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo $pending_documents; ?></p>
                            <p class="text-gray-600 text-sm">Pending Review</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm card-hover transition-all duration-300">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-comments text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo $conversations_count; ?></p>
                            <p class="text-gray-600 text-sm">AI Conversations</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- AI Contract Review -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">AI Contract Review</h2>
                        <div class="flex space-x-2">
                            <button onclick="loadConversations()" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                <i class="fas fa-history mr-1"></i>History
                            </button>
                            <button onclick="clearAllConversations()" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                <i class="fas fa-trash mr-1"></i>Clear All
                            </button>
                        </div>
                    </div>
                    
                    <!-- Conversation History -->
                    <div id="conversationHistory" class="mb-4 max-h-64 overflow-y-auto space-y-3 hidden">
                        <!-- Conversations will be loaded here -->
                    </div>
                    
                    <!-- AI Chat Interface -->
                    <div id="aiChatInterface">
                        <form id="aiContractForm" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Contract Document</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                    <input type="file" id="contractFile" name="document" accept=".pdf,.doc,.docx,.txt" class="hidden" onchange="handleContractFileSelect(event)">
                                    <div id="fileDropArea" onclick="document.getElementById('contractFile').click()" class="cursor-pointer">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-600">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, TXT (Max 10MB)</p>
                                    </div>
                                    <div id="selectedFile" class="hidden mt-2 p-2 bg-blue-50 rounded text-sm text-blue-700"></div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Instructions for AI Review</label>
                                <textarea id="aiInstructions" name="instructions" rows="3" class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="What would you like me to analyze in this contract? (e.g., identify risks, review terms, check compliance)"></textarea>
                            </div>
                            
                            <button type="submit" id="aiSubmitBtn" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50" disabled>
                                <i class="fas fa-robot mr-2"></i>Analyze Contract
                            </button>
                        </form>
                        
                        <!-- AI Response -->
                        <div id="aiResponse" class="hidden mt-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">AI Analysis Result</h3>
                            <div id="aiResponseContent" class="prose max-w-none text-gray-700"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Documents -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Documents</h2>
                        <a href="documents.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                    </div>
                    
                    <?php if (empty($user_documents)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-file-alt text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 mb-4">No documents uploaded yet</p>
                            <button onclick="openUploadModal()" class="text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-plus mr-2"></i>Upload Document
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($user_documents, 0, 5) as $doc): ?>
                                <div class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-alt text-blue-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($doc['original_name']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($doc['created_at'])); ?></p>
                                    </div>
                                    <div class="flex space-x-1">
                                        <button onclick="downloadDocument(<?php echo $doc['id']; ?>)" class="text-gray-400 hover:text-blue-600">
                                            <i class="fas fa-download text-xs"></i>
                                        </button>
                                        <button onclick="viewDocument(<?php echo $doc['id']; ?>)" class="text-gray-400 hover:text-green-600">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
    let selectedFiles = [];
    let selectedContractFile = null;
    let currentConversationId = null;

    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
    }

    // Upload Modal Functions
    function openUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        selectedFiles = [];
        document.getElementById('fileList').innerHTML = '';
        document.getElementById('uploadBtn').disabled = true;
    }

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
        selectedFiles.forEach((file) => {
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
                    location.reload();
                }, 1000);
            },
            error: function(xhr, status, error) {
                console.error('Upload error:', xhr.responseText);
                try {
                    const response = JSON.parse(xhr.responseText);
                    alert('Error uploading documents: ' + (response.error || error));
                } catch (e) {
                    alert('Error uploading documents: ' + error);
                }
            },
            complete: function() {
                uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload';
                uploadBtn.disabled = false;
            }
        });
    }

    // AI Contract Review Functions
    function handleContractFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            selectedContractFile = file;
            document.getElementById('selectedFile').innerHTML = `
                <i class="fas fa-file mr-2"></i>${file.name} (${(file.size / 1024).toFixed(1)} KB)
            `;
            document.getElementById('selectedFile').classList.remove('hidden');
            updateSubmitButton();
        }
    }

    function updateSubmitButton() {
        const instructions = document.getElementById('aiInstructions').value.trim();
        const submitBtn = document.getElementById('aiSubmitBtn');
        submitBtn.disabled = !selectedContractFile || !instructions;
    }

    document.getElementById('aiInstructions').addEventListener('input', updateSubmitButton);

    document.getElementById('aiContractForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const instructions = document.getElementById('aiInstructions').value.trim();
        if (!selectedContractFile || !instructions) {
            alert('Please select a file and provide instructions');
            return;
        }

        const formData = new FormData();
        formData.append('document', selectedContractFile);
        formData.append('instructions', instructions);
        if (currentConversationId) {
            formData.append('conversation_id', currentConversationId);
        }

        const submitBtn = document.getElementById('aiSubmitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Analyzing...';
        submitBtn.disabled = true;

        $.ajax({
            url: 'api/ai_contract_review.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                currentConversationId = response.conversation_id;
                displayAIResponse(response.response);
                document.getElementById('aiInstructions').value = '';
                selectedContractFile = null;
                document.getElementById('selectedFile').classList.add('hidden');
                document.getElementById('contractFile').value = '';
            },
            error: function(xhr, status, error) {
                console.error('AI Review error:', xhr.responseText);
                try {
                    const response = JSON.parse(xhr.responseText);
                    alert('Error: ' + (response.error || error));
                } catch (e) {
                    alert('Error analyzing contract: ' + error);
                }
            },
            complete: function() {
                submitBtn.innerHTML = '<i class="fas fa-robot mr-2"></i>Analyze Contract';
                updateSubmitButton();
            }
        });
    });

    function displayAIResponse(response) {
        const responseDiv = document.getElementById('aiResponse');
        const contentDiv = document.getElementById('aiResponseContent');
        
        // Format the response with proper line breaks and styling
        const formattedResponse = response.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        contentDiv.innerHTML = formattedResponse;
        responseDiv.classList.remove('hidden');
    }

    function loadConversations() {
        const historyDiv = document.getElementById('conversationHistory');
        
        if (historyDiv.classList.contains('hidden')) {
            $.ajax({
                url: 'api/get_conversations.php',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.conversations.length > 0) {
                        let html = '<h4 class="font-medium text-gray-800 mb-3">Recent Conversations</h4>';
                        response.conversations.forEach(conv => {
                            html += `
                                <div class="flex items-center justify-between p-3 bg-white border rounded-lg hover:bg-gray-50 cursor-pointer" onclick="loadConversation(${conv.id})">
                                    <div class="flex-1">
                                        <h5 class="font-medium text-gray-800 text-sm">${conv.title}</h5>
                                        <p class="text-xs text-gray-500">${new Date(conv.updated_at).toLocaleDateString()}</p>
                                    </div>
                                    <button onclick="event.stopPropagation(); deleteConversation(${conv.id})" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            `;
                        });
                        historyDiv.innerHTML = html;
                    } else {
                        historyDiv.innerHTML = '<p class="text-gray-500 text-sm">No conversations yet</p>';
                    }
                    historyDiv.classList.remove('hidden');
                },
                error: function() {
                    alert('Error loading conversations');
                }
            });
        } else {
            historyDiv.classList.add('hidden');
        }
    }

    function loadConversation(conversationId) {
        $.ajax({
            url: `api/get_conversations.php?conversation_id=${conversationId}`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.messages.length > 0) {
                    const lastAssistantMessage = response.messages.filter(m => m.message_type === 'assistant').pop();
                    if (lastAssistantMessage) {
                        displayAIResponse(lastAssistantMessage.content);
                        currentConversationId = conversationId;
                    }
                }
            },
            error: function() {
                alert('Error loading conversation');
            }
        });
    }

    function deleteConversation(conversationId) {
        if (confirm('Are you sure you want to delete this conversation?')) {
            $.ajax({
                url: 'api/delete_conversation.php',
                method: 'POST',
                data: JSON.stringify({ conversation_id: conversationId }),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
                        loadConversations(); // Refresh the list
                        if (currentConversationId === conversationId) {
                            document.getElementById('aiResponse').classList.add('hidden');
                            currentConversationId = null;
                        }
                    }
                },
                error: function() {
                    alert('Error deleting conversation');
                }
            });
        }
    }

    function clearAllConversations() {
        if (confirm('Are you sure you want to delete all conversations? This action cannot be undone.')) {
            $.ajax({
                url: 'api/get_conversations.php',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.conversations.length > 0) {
                        let deletePromises = response.conversations.map(conv => {
                            return $.ajax({
                                url: 'api/delete_conversation.php',
                                method: 'POST',
                                data: JSON.stringify({ conversation_id: conv.id }),
                                contentType: 'application/json'
                            });
                        });
                        
                        Promise.all(deletePromises).then(() => {
                            alert('All conversations deleted successfully');
                            document.getElementById('conversationHistory').classList.add('hidden');
                            document.getElementById('aiResponse').classList.add('hidden');
                            currentConversationId = null;
                            location.reload();
                        }).catch(() => {
                            alert('Error deleting some conversations');
                        });
                    } else {
                        alert('No conversations to delete');
                    }
                },
                error: function() {
                    alert('Error loading conversations');
                }
            });
        }
    }

    function downloadDocument(id) {
        window.open(`api/download.php?id=${id}`, '_blank');
    }

    function viewDocument(id) {
        window.open(`api/view.php?id=${id}`, '_blank');
    }
</script>