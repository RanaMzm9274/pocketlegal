/**
 * Juri AI Chat Interface
 * Enhanced AI chat system with n8n webhook integration
 * Features: Multi-chat support, file uploads, conversation management
 */

class JuriAI {
    constructor() {
        this.chats = JSON.parse(localStorage.getItem('juriChats') || '[]');
        this.currentChat = null;
        this.isWaitingForResponse = false;
        this.pendingFile = null;
        this.maxFileSize = 10 * 1024 * 1024; // 10MB
        this.supportedFormats = ['pdf', 'docx', 'rtf', 'txt'];
        
        // Webhook URLs
        this.webhooks = {
            query: 'https://n8n.srv909751.hstgr.cloud/webhook/27446a6b-9c80-417a-b06d-5b3fcdb86283',
            document: 'https://n8n.srv909751.hstgr.cloud/webhook/doc_upload'
        };

        // DOM elements
        this.elements = {};
        this.initializeElements();
        this.setupEventListeners();
        this.initializeInterface();
    }

    initializeElements() {
        this.elements = {
            modal: document.getElementById('contractReviewModal'),
            sidebar: document.getElementById('sidebar'),
            messageInput: document.getElementById('messageInput'),
            sendButton: document.getElementById('sendButton'),
            fileUpload: document.getElementById('fileUpload'),
            deleteModal: document.getElementById('deleteModal'),
            deleteModalContent: document.getElementById('deleteModalContent'),
            welcomeScreen: document.getElementById('welcomeScreen'),
            chatContainer: document.getElementById('chatContainer'),
            filePreview: document.getElementById('filePreview'),
            fileName: document.getElementById('fileName'),
            chatList: document.getElementById('chatList'),
            chatMessages: document.getElementById('chatMessages'),
            deleteBtn: document.getElementById('deleteConversationBtn'),
            charCount: document.getElementById('charCount')
        };
    }

    setupEventListeners() {
        // Message input handlers
        this.elements.messageInput.addEventListener('input', () => this.handleInputChange());
        this.elements.messageInput.addEventListener('keydown', (e) => this.handleKeyDown(e));
        
        // File upload handler
        this.elements.fileUpload.addEventListener('change', () => this.handleFileSelect());
        
        // Delete modal handlers
        this.elements.deleteModal.addEventListener('click', (e) => {
            if (e.target === this.elements.deleteModal) {
                this.hideDeleteConfirmation();
            }
        });

        // Send button handler
        this.elements.sendButton.addEventListener('click', () => this.sendMessage());

        // Drag and drop for file upload
        this.setupDragAndDrop();
    }

    setupDragAndDrop() {
        const chatArea = this.elements.chatContainer;
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            chatArea.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            chatArea.addEventListener(eventName, () => this.highlightDropZone(), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            chatArea.addEventListener(eventName, () => this.unhighlightDropZone(), false);
        });

        chatArea.addEventListener('drop', (e) => this.handleDrop(e), false);
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    highlightDropZone() {
        this.elements.chatContainer.classList.add('bg-blue-50', 'border-blue-300');
    }

    unhighlightDropZone() {
        this.elements.chatContainer.classList.remove('bg-blue-50', 'border-blue-300');
    }

    handleDrop(e) {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (this.validateFile(file)) {
                this.pendingFile = file;
                this.showFilePreview(file);
                this.updateSendButton();
            }
        }
    }

    validateFile(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (!this.supportedFormats.includes(extension)) {
            this.showNotification(`Unsupported file format. Supported formats: ${this.supportedFormats.join(', ')}`, 'error');
            return false;
        }
        
        if (file.size > this.maxFileSize) {
            this.showNotification('File size too large. Maximum size is 10MB.', 'error');
            return false;
        }
        
        return true;
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${
            type === 'error' ? 'bg-red-500 text-white' : 
            type === 'success' ? 'bg-green-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    initializeInterface() {
        this.renderChats();
        this.showWelcomeScreen();
        
        // Auto-save chats every 30 seconds
        setInterval(() => this.saveChats(), 30000);
    }

    handleInputChange() {
        const input = this.elements.messageInput;
        
        // Auto-resize textarea
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 200) + 'px';
        
        this.updateSendButton();
        this.updateCharCount();
    }

    handleKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.sendMessage();
        }
    }

    handleFileSelect() {
        const files = this.elements.fileUpload.files;
        if (files.length > 0) {
            const file = files[0];
            if (this.validateFile(file)) {
                this.pendingFile = file;
                this.showFilePreview(file);
                this.updateSendButton();
            }
        } else {
            this.clearFile();
        }
    }

    updateSendButton() {
        const hasText = this.elements.messageInput.value.trim().length > 0;
        const hasFile = this.pendingFile !== null;
        const shouldDisable = this.isWaitingForResponse || (!hasText && !hasFile);
        
        this.elements.sendButton.disabled = shouldDisable;
        
        if (shouldDisable) {
            this.elements.sendButton.classList.add('opacity-50', 'cursor-not-allowed');
            this.elements.sendButton.classList.remove('hover:scale-105');
        } else {
            this.elements.sendButton.classList.remove('opacity-50', 'cursor-not-allowed');
            this.elements.sendButton.classList.add('hover:scale-105');
        }
    }

    updateCharCount() {
        const count = this.elements.messageInput.value.length;
        this.elements.charCount.textContent = count;
        
        // Color coding for character limit
        if (count > 1800) {
            this.elements.charCount.classList.add('text-red-500');
        } else if (count > 1500) {
            this.elements.charCount.classList.add('text-orange-500');
        } else {
            this.elements.charCount.classList.remove('text-red-500', 'text-orange-500');
        }
    }

    showFilePreview(file) {
        this.elements.fileName.textContent = file.name;
        this.elements.filePreview.classList.remove('hidden');
    }

    clearFile() {
        this.pendingFile = null;
        this.elements.fileUpload.value = '';
        this.elements.filePreview.classList.add('hidden');
        this.updateSendButton();
    }

    showWelcomeScreen() {
        this.elements.welcomeScreen.classList.remove('hidden');
        this.elements.chatContainer.classList.add('hidden');
        this.elements.deleteBtn.classList.add('hidden');
    }

    showChatContainer() {
        this.elements.welcomeScreen.classList.add('hidden');
        this.elements.chatContainer.classList.remove('hidden');
        this.elements.deleteBtn.classList.remove('hidden');
    }

    createWelcomeMessage() {
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

    saveChats() {
        try {
            localStorage.setItem('juriChats', JSON.stringify(this.chats));
            return true;
        } catch (error) {
            console.error('Error saving chats:', error);
            this.showNotification('Error saving chat data', 'error');
            return false;
        }
    }

    renderChats() {
        const list = this.elements.chatList;
        list.innerHTML = '';
        
        if (this.chats.length === 0) {
            list.innerHTML = '<div class="p-4 text-gray-500 text-center text-sm">No chats yet</div>';
            return;
        }
        
        this.chats.forEach((chat, index) => {
            const item = document.createElement('div');
            item.className = `group flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all duration-200 ${
                index === this.currentChat ? 'bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200' : 'hover:bg-gray-50'
            }`;
            
            const messageCount = chat.messages.filter(m => m.from === 'user').length;
            const lastMessage = chat.messages[chat.messages.length - 1];
            const timeAgo = this.getTimeAgo(lastMessage?.timestamp);
            
            item.innerHTML = `
                <div class="w-6 h-6 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-comment text-white text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">${chat.title}</div>
                    <div class="text-xs text-gray-500 truncate">${messageCount} messages • ${timeAgo}</div>
                </div>
                <button class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition-all duration-200" onclick="event.stopPropagation(); juriAI.showDeleteConfirmation(${index})">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            `;
            
            item.onclick = () => this.switchChat(index);
            list.appendChild(item);
        });
    }

    getTimeAgo(timestamp) {
        if (!timestamp) return 'Unknown';
        
        const now = new Date();
        const time = new Date(timestamp);
        const diffInMinutes = Math.floor((now - time) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
        
        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) return `${diffInHours}h ago`;
        
        const diffInDays = Math.floor(diffInHours / 24);
        if (diffInDays < 7) return `${diffInDays}d ago`;
        
        return time.toLocaleDateString();
    }

    renderMessages() {
        const msgBox = this.elements.chatMessages;
        msgBox.innerHTML = '';
        
        if (this.currentChat === null || !this.chats[this.currentChat]) {
            return;
        }
        
        this.chats[this.currentChat].messages.forEach((msg, index) => {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex gap-4 animate-fade-in';
            messageDiv.style.animationDelay = `${index * 0.1}s`;
            
            if (msg.from === 'user') {
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 max-w-3xl">
                        <div class="text-gray-900">${this.formatMessage(msg.text)}</div>
                        ${msg.timestamp ? `<div class="text-xs text-gray-400 mt-2">${this.formatTimestamp(msg.timestamp)}</div>` : ''}
                    </div>
                `;
            } else {
                const isError = msg.isError || false;
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r ${isError ? 'from-red-500 to-red-600' : 'from-green-500 to-teal-500'} text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas ${isError ? 'fa-exclamation-triangle' : 'fa-robot'} text-sm"></i>
                    </div>
                    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 max-w-3xl">
                        <div class="prose prose-sm max-w-none text-gray-900">${this.formatMessage(msg.text)}</div>
                        ${msg.timestamp ? `<div class="text-xs text-gray-400 mt-2">${this.formatTimestamp(msg.timestamp)}</div>` : ''}
                    </div>
                `;
            }
            
            msgBox.appendChild(messageDiv);
        });
        
        this.scrollToBottom();
    }

    formatMessage(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-sm font-mono">$1</code>')
            .replace(/\n/g, '<br>');
    }

    formatTimestamp(timestamp) {
        return new Date(timestamp).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
    }

    scrollToBottom() {
        setTimeout(() => {
            this.elements.chatMessages.scrollTop = this.elements.chatMessages.scrollHeight;
        }, 100);
    }

    startNewChat() {
        const title = `Chat ${this.chats.length + 1}`;
        const newChat = { 
            title, 
            messages: [this.createWelcomeMessage()],
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        
        this.chats.push(newChat);
        this.currentChat = this.chats.length - 1;
        
        this.saveChats();
        this.renderChats();
        this.showChatContainer();
        this.renderMessages();
        
        this.elements.messageInput.focus();
        this.clearFile();
    }

    switchChat(index) {
        if (index < 0 || index >= this.chats.length) return;
        
        this.currentChat = index;
        this.renderChats();
        this.showChatContainer();
        this.renderMessages();
        
        this.elements.messageInput.focus();
        this.clearFile();
    }

    sendExampleMessage(message) {
        if (this.currentChat === null) {
            this.startNewChat();
        }
        this.elements.messageInput.value = message;
        setTimeout(() => this.sendMessage(), 100);
    }

    showDeleteConfirmation(index = null) {
        if (index !== null) {
            window.chatToDelete = index;
        } else {
            window.chatToDelete = this.currentChat;
        }
        
        if (window.chatToDelete === null || this.chats.length === 0) {
            return;
        }
        
        this.elements.deleteModal.classList.remove('opacity-0', 'invisible');
        this.elements.deleteModal.classList.add('opacity-100', 'visible');
        this.elements.deleteModalContent.classList.remove('translate-y-5');
    }

    hideDeleteConfirmation() {
        this.elements.deleteModal.classList.remove('opacity-100', 'visible');
        this.elements.deleteModal.classList.add('opacity-0', 'invisible');
        this.elements.deleteModalContent.classList.add('translate-y-5');
        window.chatToDelete = null;
    }

    deleteCurrentChat() {
        const indexToDelete = window.chatToDelete;
        if (indexToDelete === null || indexToDelete >= this.chats.length) {
            this.hideDeleteConfirmation();
            return;
        }
        
        this.chats.splice(indexToDelete, 1);
        
        if (this.currentChat === indexToDelete) {
            if (this.chats.length > 0) {
                this.currentChat = Math.min(this.currentChat, this.chats.length - 1);
                this.showChatContainer();
            } else {
                this.currentChat = null;
                this.showWelcomeScreen();
            }
        } else if (this.currentChat > indexToDelete) {
            this.currentChat--;
        }
        
        this.saveChats();
        this.renderChats();
        this.renderMessages();
        this.hideDeleteConfirmation();
        
        this.showNotification('Chat deleted successfully', 'success');
    }

    async sendMessage() {
        const text = this.elements.messageInput.value.trim();
        
        if (!text && !this.pendingFile) {
            return;
        }
        
        if (this.currentChat === null) {
            this.startNewChat();
            setTimeout(() => this.sendMessage(), 100);
            return;
        }
        
        // Prepare display text
        let displayText = text;
        if (this.pendingFile) {
            displayText = (text ? `${text}<br><br>` : '') + `📄 <strong>File:</strong> ${this.pendingFile.name}`;
        }
        
        // Add user message
        const userMessage = {
            from: 'user', 
            text: displayText,
            timestamp: new Date().toISOString()
        };
        
        this.chats[this.currentChat].messages.push(userMessage);
        this.chats[this.currentChat].updatedAt = new Date().toISOString();
        
        // Update title if first user message
        const userMessages = this.chats[this.currentChat].messages.filter(m => m.from === 'user');
        if (userMessages.length === 1 && text) {
            const title = text.length > 40 ? text.substring(0, 40) + '...' : text;
            this.chats[this.currentChat].title = title;
        }
        
        // Clear input and file
        this.elements.messageInput.value = '';
        this.elements.messageInput.style.height = 'auto';
        const fileToSend = this.pendingFile;
        this.clearFile();
        
        this.saveChats();
        this.renderChats();
        this.renderMessages();
        this.showTypingIndicator();
        
        this.isWaitingForResponse = true;
        this.updateSendButton();
        
        try {
            const response = await this.callWebhook(text, fileToSend);
            
            const aiMessage = {
                from: 'ai', 
                text: response.message,
                timestamp: new Date().toISOString(),
                webhookUsed: fileToSend ? 'document' : 'query'
            };
            
            this.chats[this.currentChat].messages.push(aiMessage);
            this.chats[this.currentChat].updatedAt = new Date().toISOString();
            
        } catch (error) {
            console.error('Error calling webhook:', error);
            const errorMessage = this.getErrorMessage(error, !!fileToSend);
            
            const aiMessage = {
                from: 'ai', 
                text: errorMessage,
                timestamp: new Date().toISOString(),
                isError: true
            };
            
            this.chats[this.currentChat].messages.push(aiMessage);
        } finally {
            this.hideTypingIndicator();
            this.saveChats();
            this.renderChats();
            this.renderMessages();
            this.isWaitingForResponse = false;
            this.updateSendButton();
        }
    }

    async callWebhook(text, file) {
        const webhookUrl = file ? this.webhooks.document : this.webhooks.query;
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000); // 15s timeout
        
        let response;
        
        try {
            if (file) {
                const formData = new FormData();
                if (text) formData.append('message', text);
                formData.append('data', file);
                
                response = await fetch(webhookUrl, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors',
                    signal: controller.signal
                });
            } else {
                response = await fetch(webhookUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text }),
                    mode: 'cors',
                    signal: controller.signal
                });
            }
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type') || '';
            let aiResponse;
            
            if (contentType.includes('application/json')) {
                const data = await response.json();
                console.log('Webhook response:', JSON.stringify(data, null, 2));
                aiResponse = data.reply || data.message || data.data?.message || data.response || data.data?.response || JSON.stringify(data);
            } else {
                aiResponse = await response.text();
            }
            
            if (!aiResponse.trim()) {
                aiResponse = 'Received an empty response from the server.';
            }
            
            return { message: aiResponse };
            
        } catch (error) {
            clearTimeout(timeoutId);
            throw error;
        }
    }

    getErrorMessage(error, hasFile = false) {
        const errorMsg = error.message;
        
        if (errorMsg.includes('CORS') || errorMsg.includes('Failed to fetch')) {
            return `**⚠️ Connection Issue**

Unable to connect to the AI service due to a CORS or network error.

**Solutions:**
- Ensure your n8n webhook is configured with proper CORS headers:
  - Access-Control-Allow-Origin: *
  - Access-Control-Allow-Methods: POST, GET, OPTIONS
  - Access-Control-Allow-Headers: Content-Type
- Check your internet connection
- Try again in a few moments

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('500')) {
            return `**⚠️ Server Error**

The server encountered an internal error.

**Solutions:**
- Ensure the uploaded file is a valid PDF, DOCX, or RTF
- Check your n8n workflow logs for errors
- Try again later

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('Empty') || errorMsg.includes('JSON')) {
            return `**📄 Response Issue**

The server returned an invalid or empty response.

**Solutions:**
- Verify your n8n workflow returns a JSON response
- Test the webhook with a tool like Postman
- Ensure the workflow is active

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('404')) {
            return `**🔍 Webhook Not Found**

The webhook URL is incorrect or the endpoint doesn't exist.

**Solutions:**
- Verify the webhook URL
- Ensure the n8n workflow is active

**Error:** ${errorMsg}`;
        }
        
        return `**❌ Unexpected Error**

An error occurred while processing your request.

**Solutions:**
- Try refreshing the page
- Check the browser console for details
- Contact support if the issue persists

**Error:** ${errorMsg}`;
    }

    showTypingIndicator() {
        const msgBox = this.elements.chatMessages;
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
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    open() {
        this.elements.modal.classList.remove('hidden');
        if (this.chats.length === 0 || this.currentChat === null) {
            this.startNewChat();
        }
        this.elements.messageInput.focus();
    }

    close() {
        this.elements.modal.classList.add('hidden');
    }

    // Export/Import functionality
    exportChats() {
        const dataStr = JSON.stringify(this.chats, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        
        const link = document.createElement('a');
        link.href = URL.createObjectURL(dataBlob);
        link.download = `juri-ai-chats-${new Date().toISOString().split('T')[0]}.json`;
        link.click();
        
        this.showNotification('Chats exported successfully', 'success');
    }

    importChats(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const importedChats = JSON.parse(e.target.result);
                if (Array.isArray(importedChats)) {
                    this.chats = [...this.chats, ...importedChats];
                    this.saveChats();
                    this.renderChats();
                    this.showNotification('Chats imported successfully', 'success');
                } else {
                    throw new Error('Invalid chat format');
                }
            } catch (error) {
                this.showNotification('Error importing chats: Invalid file format', 'error');
            }
        };
        reader.readAsText(file);
    }
}

// Initialize Juri AI when DOM is loaded
let juriAI;
document.addEventListener('DOMContentLoaded', () => {
    juriAI = new JuriAI();
});

// Global functions for backwards compatibility
function openContractReviewModal() {
    juriAI.open();
}

function closeContractReviewModal() {
    juriAI.close();
}

function startNewChat() {
    juriAI.startNewChat();
}

function sendMessage() {
    juriAI.sendMessage();
}

function sendExampleMessage(message) {
    juriAI.sendExampleMessage(message);
}

function clearFile() {
    juriAI.clearFile();
}

function showDeleteConfirmation(index) {
    juriAI.showDeleteConfirmation(index);
}

function hideDeleteConfirmation() {
    juriAI.hideDeleteConfirmation();
}

function deleteCurrentChat() {
    juriAI.deleteCurrentChat();
}