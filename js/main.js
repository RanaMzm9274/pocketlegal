/**
 * Main Dashboard JavaScript
 * Handles all dashboard functionality except Juri AI chat
 * Features: File uploads, modals, conversations, document management
 */

class Dashboard {
    constructor() {
        this.selectedFiles = [];
        this.chatFile = null;
        this.currentConversationId = null;
        this.conversations = [];
        this.maxFileSize = 10 * 1024 * 1024; // 10MB
        this.supportedFormats = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
        
        this.initializeEventListeners();
        this.setupDragAndDrop();
    }

    initializeEventListeners() {
        // File upload handlers
        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }

        // Chat functionality
        const chatInput = document.getElementById('chatInput');
        if (chatInput) {
            chatInput.addEventListener('input', () => this.updateCharCount());
            chatInput.addEventListener('keydown', (e) => this.handleChatKeyDown(e));
        }

        const chatFileInput = document.getElementById('chatFileInput');
        if (chatFileInput) {
            chatFileInput.addEventListener('change', (e) => this.handleChatFileSelect(e));
        }

        // Search functionality
        const conversationSearch = document.getElementById('conversationSearch');
        if (conversationSearch) {
            conversationSearch.addEventListener('input', (e) => this.filterConversations(e.target.value));
        }

        // Modal close handlers
        this.setupModalHandlers();
    }

    setupModalHandlers() {
        // Close modals when clicking outside
        const modals = ['uploadModal', 'aiChatModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal(modalId);
                    }
                });
            }
        });
    }

    setupDragAndDrop() {
        const uploadArea = document.querySelector('#uploadModal .border-dashed');
        if (!uploadArea) return;
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => this.highlight(uploadArea), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => this.unhighlight(uploadArea), false);
        });
        
        uploadArea.addEventListener('drop', (e) => this.handleDrop(e), false);
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    highlight(element) {
        element.classList.add('border-blue-500', 'bg-blue-50');
    }

    unhighlight(element) {
        element.classList.remove('border-blue-500', 'bg-blue-50');
    }

    handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        this.selectedFiles = Array.from(files).filter(file => this.validateFile(file));
        this.displayFileList();
        this.updateUploadButton();
    }

    validateFile(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (!this.supportedFormats.includes(extension)) {
            this.showNotification(`Unsupported file format: ${file.name}. Supported formats: ${this.supportedFormats.join(', ')}`, 'error');
            return false;
        }
        
        if (file.size > this.maxFileSize) {
            this.showNotification(`File too large: ${file.name}. Maximum size is 10MB.`, 'error');
            return false;
        }
        
        return true;
    }

    showNotification(message, type = 'info', duration = 4000) {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.dashboard-notification');
        existingNotifications.forEach(notif => notif.remove());

        const notification = document.createElement('div');
        notification.className = `dashboard-notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-0 ${
            type === 'error' ? 'bg-red-500 text-white' : 
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <i class="fas ${
                    type === 'error' ? 'fa-exclamation-circle' : 
                    type === 'success' ? 'fa-check-circle' : 
                    type === 'warning' ? 'fa-exclamation-triangle' :
                    'fa-info-circle'
                }"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after duration
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }

    // File Upload Functionality
    handleFileSelect(event) {
        const files = Array.from(event.target.files).filter(file => this.validateFile(file));
        this.selectedFiles = files;
        this.displayFileList();
        this.updateUploadButton();
    }

    displayFileList() {
        const fileList = document.getElementById('fileList');
        if (!fileList) return;
        
        fileList.innerHTML = '';
        
        if (this.selectedFiles.length === 0) {
            fileList.innerHTML = '<div class="text-gray-500 text-sm text-center py-4">No files selected</div>';
            return;
        }
        
        this.selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors';
            
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            const icon = this.getFileIcon(file.name);
            
            fileItem.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas ${icon} text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${file.name}</div>
                        <div class="text-xs text-gray-500">${sizeInMB} MB</div>
                    </div>
                </div>
                <button onclick="dashboard.removeFile(${index})" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        });
    }

    getFileIcon(fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'fa-file-pdf',
            'doc': 'fa-file-word',
            'docx': 'fa-file-word',
            'txt': 'fa-file-alt',
            'rtf': 'fa-file-alt'
        };
        return iconMap[extension] || 'fa-file';
    }

    removeFile(index) {
        this.selectedFiles.splice(index, 1);
        this.displayFileList();
        this.updateUploadButton();
    }

    updateUploadButton() {
        const uploadBtn = document.getElementById('uploadBtn');
        if (uploadBtn) {
            uploadBtn.disabled = this.selectedFiles.length === 0;
            uploadBtn.classList.toggle('opacity-50', this.selectedFiles.length === 0);
        }
    }

    async uploadFiles() {
        if (this.selectedFiles.length === 0) {
            this.showNotification('No files selected for upload', 'warning');
            return;
        }

        const formData = new FormData();
        this.selectedFiles.forEach((file, index) => {
            formData.append('documents[]', file);
        });

        const uploadBtn = document.getElementById('uploadBtn');
        const originalText = uploadBtn.innerHTML;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        uploadBtn.disabled = true;

        try {
            const response = await fetch('api/upload.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                this.showNotification(`Successfully uploaded ${this.selectedFiles.length} file(s)`, 'success');
                this.closeModal('uploadModal'); // Fixed: Use closeModal instead of closeUploadModal
                
                // Refresh page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.error || 'Upload failed');
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showNotification(`Error uploading files: ${error.message}`, 'error');
        } finally {
            uploadBtn.innerHTML = originalText;
            uploadBtn.disabled = false;
        }
    }

    // AI Chat Modal Functionality
    handleChatKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.sendChatMessage();
        }
    }

    handleChatFileSelect(event) {
        const file = event.target.files[0];
        if (file && this.validateFile(file)) {
            this.chatFile = file;
            const preview = document.getElementById('chatFilePreview');
            if (preview) {
                preview.innerHTML = `
                    <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-2 rounded-lg border border-blue-200">
                        <i class="fas ${this.getFileIcon(file.name)} text-blue-600"></i>
                        <span class="text-sm font-medium">${file.name}</span>
                        <button onclick="dashboard.removeChatFile()" class="text-red-500 hover:text-red-700 ml-2">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                `;
            }
        }
    }

    removeChatFile() {
        this.chatFile = null;
        const preview = document.getElementById('chatFilePreview');
        const input = document.getElementById('chatFileInput');
        
        if (preview) preview.innerHTML = '';
        if (input) input.value = '';
    }

    updateCharCount() {
        const input = document.getElementById('chatInput');
        const charCount = document.getElementById('charCount');
        
        if (input && charCount) {
            const count = input.value.length;
            charCount.textContent = count;
            
            // Color coding for character limit
            charCount.classList.remove('text-red-500', 'text-orange-500', 'text-gray-500');
            if (count > 1800) {
                charCount.classList.add('text-red-500');
            } else if (count > 1500) {
                charCount.classList.add('text-orange-500');
            } else {
                charCount.classList.add('text-gray-500');
            }
        }
    }

    async sendChatMessage() {
        const input = document.getElementById('chatInput');
        const query = input.value.trim();
        
        if (!query) {
            this.showNotification('Please enter a message', 'warning');
            return;
        }

        // Add user message to chat
        this.addMessageToChat('user', query, this.chatFile ? this.chatFile.name : null);
        
        // Clear input
        input.value = '';
        this.updateCharCount();
        
        // Show typing indicator
        this.addTypingIndicator();
        
        // Prepare form data
        const formData = new FormData();
        formData.append('query', query);
        
        if (this.currentConversationId) {
            formData.append('conversation_id', this.currentConversationId);
        }
        
        if (this.chatFile) {
            formData.append('document', this.chatFile);
        }
        
        try {
            const response = await fetch('api/ai_contract_review.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            this.removeTypingIndicator();
            
            if (result.success) {
                // Update conversation ID if new conversation
                if (!this.currentConversationId && result.conversation_id) {
                    this.currentConversationId = result.conversation_id;
                    const deleteBtn = document.getElementById('deleteConversationBtn');
                    if (deleteBtn) deleteBtn.classList.remove('hidden');
                    this.loadConversations();
                }
                
                // Add AI response
                this.addMessageToChat('assistant', result.response);
                
                if (result.webhook_success) {
                    console.log('✅ Webhook response received successfully');
                } else {
                    console.log('⚠️ AI analysis service unavailable');
                }
            } else {
                this.addMessageToChat('assistant', 'I apologize, but I encountered an error processing your request. Please try again.');
                this.showNotification('Error processing request', 'error');
            }
        } catch (error) {
            console.error('AI Query Error:', error);
            this.removeTypingIndicator();
            this.addMessageToChat('assistant', 'I apologize, but I encountered an error processing your request. Please try again.');
            this.showNotification('Network error occurred', 'error');
        } finally {
            this.removeChatFile();
        }
    }

    addMessageToChat(sender, message, documentName = null, animate = true) {
        const chatMessages = document.getElementById('chatMessages');
        if (!chatMessages) return;
        
        const container = chatMessages.querySelector('.space-y-4') || chatMessages;
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex items-start space-x-3 ${animate ? 'animate-fade-in' : ''}`;
        
        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white text-sm">
                    <i class="fas fa-user"></i>
                </div>
                <div class="bg-blue-600 text-white p-4 rounded-lg shadow-sm max-w-2xl">
                    ${documentName ? `<div class="mb-2 text-blue-100 text-sm"><i class="fas fa-paperclip mr-1"></i>${documentName}</div>` : ''}
                    <p class="whitespace-pre-wrap">${this.escapeHtml(message)}</p>
                    <div class="text-xs text-blue-100 mt-2">${new Date().toLocaleTimeString()}</div>
                </div>
            `;
        } else if (sender === 'assistant') {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm max-w-2xl border border-gray-200">
                    <div class="text-gray-800 whitespace-pre-wrap">${this.formatAIMessage(message)}</div>
                    <div class="text-xs text-gray-400 mt-2">${new Date().toLocaleTimeString()}</div>
                </div>
            `;
        }
        
        container.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    formatAIMessage(message) {
        return message
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-sm font-mono">$1</code>');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    addTypingIndicator() {
        const chatMessages = document.getElementById('chatMessages');
        if (!chatMessages) return;
        
        const container = chatMessages.querySelector('.space-y-4') || chatMessages;
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'flex items-start space-x-3';
        typingDiv.innerHTML = `
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                <i class="fas fa-robot"></i>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
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

    removeTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Conversation Management
    async loadConversations() {
        try {
            const response = await fetch('api/get_conversations.php');
            const result = await response.json();
            
            if (result.success) {
                this.conversations = result.conversations;
                this.displayConversations();
            } else {
                console.error('Error loading conversations:', result.error);
            }
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }

    displayConversations() {
        const conversationsList = document.getElementById('conversationsList');
        if (!conversationsList) return;
        
        if (this.conversations.length === 0) {
            conversationsList.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-comments text-3xl mb-3 text-gray-300"></i>
                    <p class="text-sm font-medium">No conversations yet</p>
                    <p class="text-xs">Start chatting to create your first conversation</p>
                </div>
            `;
            return;
        }
        
        conversationsList.innerHTML = this.conversations.map(conv => `
            <div class="conversation-item p-3 rounded-lg cursor-pointer hover:bg-gray-100 mb-2 border border-transparent transition-all duration-200" 
                 onclick="dashboard.loadConversation(${conv.id})" data-conversation-id="${conv.id}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-sm truncate">${conv.title}</h4>
                        <p class="text-xs text-gray-500 truncate mt-1">${conv.last_message || 'No messages'}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-400">${conv.message_count} messages</span>
                            <span class="text-xs text-gray-400">${this.formatDate(conv.updated_at)}</span>
                        </div>
                    </div>
                    <button onclick="event.stopPropagation(); dashboard.deleteConversation(${conv.id})" 
                            class="text-gray-400 hover:text-red-500 ml-2 p-1 rounded hover:bg-red-50 transition-colors">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    filterConversations(searchTerm) {
        const items = document.querySelectorAll('.conversation-item');
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const matches = text.includes(searchTerm.toLowerCase());
            item.style.display = matches ? 'block' : 'none';
        });
    }

    async loadConversation(conversationId) {
        this.currentConversationId = conversationId;
        
        // Update UI to show active conversation
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'border-blue-200');
        });
        
        const activeItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
        if (activeItem) {
            activeItem.classList.add('bg-blue-50', 'border-blue-200');
        }
        
        const deleteBtn = document.getElementById('deleteConversationBtn');
        if (deleteBtn) deleteBtn.classList.remove('hidden');
        
        try {
            const response = await fetch(`api/get_conversations.php?conversation_id=${conversationId}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayConversationMessages(result.messages);
            } else {
                this.showNotification('Error loading conversation', 'error');
            }
        } catch (error) {
            console.error('Error loading conversation:', error);
            this.showNotification('Network error loading conversation', 'error');
        }
    }

    displayConversationMessages(messages) {
        const chatMessages = document.getElementById('chatMessages');
        if (!chatMessages) return;
        
        chatMessages.innerHTML = '<div class="space-y-4"></div>';
        
        messages.forEach(message => {
            this.addMessageToChat(message.message_type, message.content, message.document_name, false);
        });
    }

    async deleteConversation(conversationId) {
        if (!confirm('Are you sure you want to delete this conversation? This action cannot be undone.')) {
            return;
        }
        
        try {
            const response = await fetch('api/delete_conversation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ conversation_id: conversationId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Conversation deleted successfully', 'success');
                this.loadConversations();
                
                if (this.currentConversationId === conversationId) {
                    this.startNewConversation();
                }
            } else {
                this.showNotification('Error deleting conversation: ' + result.error, 'error');
            }
        } catch (error) {
            console.error('Error deleting conversation:', error);
            this.showNotification('Network error deleting conversation', 'error');
        }
    }

    formatDate(dateString) {
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

    // Modal Management
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            
            // Special handling for specific modals
            if (modalId === 'aiChatModal') {
                this.loadConversations();
                this.startNewConversation();
            }
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            
            // Reset state for specific modals
            if (modalId === 'uploadModal') {
                this.resetUploadModal();
            } else if (modalId === 'aiChatModal') {
                this.resetChatState();
            }
        }
    }

    resetUploadModal() {
        this.selectedFiles = [];
        const fileList = document.getElementById('fileList');
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtn');
        
        if (fileList) fileList.innerHTML = '';
        if (fileInput) fileInput.value = '';
        if (uploadBtn) uploadBtn.disabled = true;
    }

    resetChatState() {
        this.currentConversationId = null;
        this.chatFile = null;
        
        const chatInput = document.getElementById('chatInput');
        const chatFilePreview = document.getElementById('chatFilePreview');
        const deleteBtn = document.getElementById('deleteConversationBtn');
        
        if (chatInput) chatInput.value = '';
        if (chatFilePreview) chatFilePreview.innerHTML = '';
        if (deleteBtn) deleteBtn.classList.add('hidden');
        
        this.updateCharCount();
    }

    startNewConversation() {
        this.resetChatState();
        const chatMessages = document.getElementById('chatMessages');
        
        if (chatMessages) {
            chatMessages.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm max-w-2xl border border-gray-200">
                            <p class="text-gray-800">Hello! I'm your AI contract assistant. Upload a contract and ask me anything about it - I can help with analysis, risk assessment, clause explanations, and more.</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Clear active conversation styling
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-blue-50', 'border-blue-200');
        });
    }

    // Feature Placeholders
    showComingSoon(feature) {
        this.showNotification(`${feature} functionality will be implemented in the next phase.`, 'info');
    }

    toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        if (submenu) {
            submenu.classList.toggle('hidden');
        }
    }
}

// Initialize Dashboard
let dashboard;
document.addEventListener('DOMContentLoaded', () => {
    dashboard = new Dashboard();
});

// Global functions for backwards compatibility
function handleFileSelect(event) {
    dashboard.handleFileSelect(event);
}

function displayFileList() {
    dashboard.displayFileList();
}

function removeFile(index) {
    dashboard.removeFile(index);
}

function uploadFiles() {
    dashboard.uploadFiles();
}

function handleChatFileSelect(event) {
    dashboard.handleChatFileSelect(event);
}

function removeChatFile() {
    dashboard.removeChatFile();
}

function sendChatMessage() {
    dashboard.sendChatMessage();
}

function addMessageToChat(sender, message, documentName = null, animate = true) {
    dashboard.addMessageToChat(sender, message, documentName, animate);
}

function addTypingIndicator() {
    dashboard.addTypingIndicator();
}

function removeTypingIndicator() {
    dashboard.removeTypingIndicator();
}

function updateCharCount() {
    dashboard.updateCharCount();
}

function loadConversations() {
    dashboard.loadConversations();
}

function displayConversations() {
    dashboard.displayConversations();
}

function loadConversation(conversationId) {
    dashboard.loadConversation(conversationId);
}

function deleteConversation(conversationId) {
    dashboard.deleteConversation(conversationId);
}

function deleteCurrentConversation() {
    if (dashboard.currentConversationId) {
        dashboard.deleteConversation(dashboard.currentConversationId);
    }
}

function startNewConversation() {
    dashboard.startNewConversation();
}

function formatDate(dateString) {
    return dashboard.formatDate(dateString);
}

function openAIChat() {
    dashboard.openModal('aiChatModal');
}

function closeAIChat() {
    dashboard.closeModal('aiChatModal');
}

function openUploadModal() {
    dashboard.openModal('uploadModal');
}

function closeUploadModal() {
    dashboard.closeModal('uploadModal');
}

function openCreateDocumentModal() {
    dashboard.showComingSoon('Create Document');
}

function openESignatureModal() {
    dashboard.showComingSoon('eSignature');
}

function openLegalResearchModal() {
    dashboard.showComingSoon('Legal Research');
}

function openComplianceModal() {
    dashboard.showComingSoon('Compliance');
}

function openSettingsModal() {
    dashboard.showComingSoon('Settings');
}

function openHelpModal() {
    dashboard.showComingSoon('Help & Support');
}

function toggleSubmenu(id) {
    dashboard.toggleSubmenu(id);
}

// Additional utility functions that might be useful
function showNotification(message, type = 'info', duration = 4000) {
    dashboard.showNotification(message, type, duration);
}

function closeModal(modalId) {
    dashboard.closeModal(modalId);
}

function openModal(modalId) {
    dashboard.openModal(modalId);
}

// Export dashboard for module usage if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Dashboard;
}