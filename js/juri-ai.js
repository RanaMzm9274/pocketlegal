/**
 * Juri AI Chat Interface
 * Enhanced AI chat system with n8n webhook integration
 * Features: Multi-chat support, file uploads, conversation management, edit/copy/delete options
 */

class JuriAI {
    constructor() {
        this.chats = JSON.parse(localStorage.getItem('juriChats') || '[]');
        this.currentChat = null;
        this.isWaitingForResponse = false;
        this.pendingFile = null;
        this.maxFileSize = 10 * 1024 * 1024; // 10MB
        this.supportedFormats = ['pdf', 'docx', 'rtf', 'txt'];
        this.editingMessageId = null;
        
        // Webhook URLs - Fixed for better response handling
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
            if (this.editingMessageId) {
                this.saveEditedMessage();
            } else {
                this.sendMessage();
            }
        } else if (e.key === 'Escape' && this.editingMessageId) {
            this.cancelEdit();
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
        
        // Update button text based on editing state
        const buttonIcon = this.elements.sendButton.querySelector('i');
        if (this.editingMessageId) {
            buttonIcon.className = 'fas fa-check text-sm';
            this.elements.sendButton.title = 'Save changes (Enter)';
        } else {
            buttonIcon.className = 'fas fa-paper-plane text-sm';
            this.elements.sendButton.title = 'Send message (Enter)';
        }
        
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
            id: Date.now() + '_welcome',
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
        
        // Add "Delete All Chats" button if there are chats
        if (this.chats.length > 0) {
            const deleteAllBtn = document.createElement('div');
            deleteAllBtn.className = 'p-3 border-b border-gray-200';
            deleteAllBtn.innerHTML = `
                <button onclick="juriAI.showDeleteAllConfirmation()" class="w-full flex items-center gap-3 px-4 py-3 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-trash text-sm"></i>
                    <span class="font-medium">Delete All Chats</span>
                </button>
            `;
            list.appendChild(deleteAllBtn);
        }
        
        if (this.chats.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'p-4 text-gray-500 text-center text-sm';
            emptyState.textContent = 'No chats yet';
            list.appendChild(emptyState);
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
            messageDiv.setAttribute('data-message-id', msg.id);
            
            if (msg.from === 'user') {
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 max-w-3xl group">
                        <div class="text-gray-900" id="message-content-${msg.id}">${this.formatMessage(msg.text)}</div>
                        ${msg.timestamp ? `<div class="text-xs text-gray-400 mt-2 flex items-center justify-between">
                            <span>${this.formatTimestamp(msg.timestamp)}</span>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                <button onclick="juriAI.editMessage('${msg.id}')" class="text-gray-400 hover:text-blue-600 p-1 rounded" title="Edit message">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button onclick="juriAI.copyMessage('${msg.id}')" class="text-gray-400 hover:text-green-600 p-1 rounded" title="Copy message">
                                    <i class="fas fa-copy text-xs"></i>
                                </button>
                            </div>
                        </div>` : ''}
                    </div>
                `;
            } else {
                const isError = msg.isError || false;
                messageDiv.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r ${isError ? 'from-red-500 to-red-600' : 'from-green-500 to-teal-500'} text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas ${isError ? 'fa-exclamation-triangle' : 'fa-robot'} text-sm"></i>
                    </div>
                    <div class="flex-1 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 max-w-3xl group">
                        <div class="prose prose-sm max-w-none text-gray-900" id="message-content-${msg.id}">${this.formatMessage(msg.text)}</div>
                        ${msg.timestamp ? `<div class="text-xs text-gray-400 mt-2 flex items-center justify-between">
                            <span>${this.formatTimestamp(msg.timestamp)}</span>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                <button onclick="juriAI.copyMessage('${msg.id}')" class="text-gray-400 hover:text-green-600 p-1 rounded" title="Copy response">
                                    <i class="fas fa-copy text-xs"></i>
                                </button>
                                <button onclick="juriAI.regenerateResponse('${msg.id}')" class="text-gray-400 hover:text-blue-600 p-1 rounded" title="Regenerate response">
                                    <i class="fas fa-redo text-xs"></i>
                                </button>
                            </div>
                        </div>` : ''}
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
        this.cancelEdit(); // Cancel any ongoing edits
    }

    switchChat(index) {
        if (index < 0 || index >= this.chats.length) return;
        
        this.currentChat = index;
        this.renderChats();
        this.showChatContainer();
        this.renderMessages();
        
        this.elements.messageInput.focus();
        this.clearFile();
        this.cancelEdit(); // Cancel any ongoing edits
    }

    sendExampleMessage(message) {
        if (this.currentChat === null) {
            this.startNewChat();
        }
        this.elements.messageInput.value = message;
        setTimeout(() => this.sendMessage(), 100);
    }

    // Edit functionality
    editMessage(messageId) {
        if (this.isWaitingForResponse) return;
        
        const chat = this.chats[this.currentChat];
        const message = chat.messages.find(m => m.id === messageId);
        
        if (!message || message.from !== 'user') return;
        
        this.editingMessageId = messageId;
        this.elements.messageInput.value = message.text.replace(/<br>/g, '\n').replace(/<[^>]*>/g, '');
        this.elements.messageInput.focus();
        this.updateSendButton();
        
        // Highlight the message being edited
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        if (messageElement) {
            messageElement.classList.add('ring-2', 'ring-blue-300', 'bg-blue-50');
        }
        
        this.showNotification('Editing message. Press Enter to save or Escape to cancel.', 'info');
    }

    saveEditedMessage() {
        if (!this.editingMessageId) return;
        
        const newText = this.elements.messageInput.value.trim();
        if (!newText) {
            this.showNotification('Message cannot be empty', 'error');
            return;
        }
        
        const chat = this.chats[this.currentChat];
        const messageIndex = chat.messages.findIndex(m => m.id === this.editingMessageId);
        
        if (messageIndex === -1) return;
        
        // Update the message
        chat.messages[messageIndex].text = newText;
        chat.messages[messageIndex].edited = true;
        chat.messages[messageIndex].editedAt = new Date().toISOString();
        
        // Remove all messages after the edited one (since context has changed)
        chat.messages = chat.messages.slice(0, messageIndex + 1);
        
        this.saveChats();
        this.renderMessages();
        this.cancelEdit();
        
        this.showNotification('Message updated successfully', 'success');
    }

    cancelEdit() {
        if (this.editingMessageId) {
            // Remove highlight from edited message
            const messageElement = document.querySelector(`[data-message-id="${this.editingMessageId}"]`);
            if (messageElement) {
                messageElement.classList.remove('ring-2', 'ring-blue-300', 'bg-blue-50');
            }
        }
        
        this.editingMessageId = null;
        this.elements.messageInput.value = '';
        this.elements.messageInput.style.height = 'auto';
        this.updateSendButton();
    }

    // Copy functionality
    async copyMessage(messageId) {
        const chat = this.chats[this.currentChat];
        const message = chat.messages.find(m => m.id === messageId);
        
        if (!message) return;
        
        try {
            // Remove HTML formatting for plain text copy
            const plainText = message.text
                .replace(/<br>/g, '\n')
                .replace(/<strong>(.*?)<\/strong>/g, '**$1**')
                .replace(/<em>(.*?)<\/em>/g, '*$1*')
                .replace(/<code[^>]*>(.*?)<\/code>/g, '`$1`')
                .replace(/<[^>]*>/g, '');
            
            await navigator.clipboard.writeText(plainText);
            this.showNotification('Message copied to clipboard', 'success');
        } catch (error) {
            console.error('Failed to copy message:', error);
            this.showNotification('Failed to copy message', 'error');
        }
    }

    // Regenerate response functionality
    async regenerateResponse(messageId) {
        if (this.isWaitingForResponse) return;
        
        const chat = this.chats[this.currentChat];
        const messageIndex = chat.messages.findIndex(m => m.id === messageId);
        
        if (messageIndex === -1 || chat.messages[messageIndex].from !== 'ai') return;
        
        // Find the user message that prompted this response
        let userMessageIndex = messageIndex - 1;
        while (userMessageIndex >= 0 && chat.messages[userMessageIndex].from !== 'user') {
            userMessageIndex--;
        }
        
        if (userMessageIndex < 0) return;
        
        const userMessage = chat.messages[userMessageIndex];
        
        // Remove the AI response and any messages after it
        chat.messages = chat.messages.slice(0, messageIndex);
        
        this.renderMessages();
        this.showTypingIndicator();
        
        this.isWaitingForResponse = true;
        this.updateSendButton();
        
        try {
            const response = await this.callWebhook(userMessage.text, null);
            
            const aiMessage = {
                id: Date.now() + '_ai',
                from: 'ai', 
                text: response.message,
                timestamp: new Date().toISOString(),
                regenerated: true
            };
            
            chat.messages.push(aiMessage);
            chat.updatedAt = new Date().toISOString();
            
        } catch (error) {
            console.error('Error regenerating response:', error);
            const errorMessage = this.getErrorMessage(error, false);
            
            const aiMessage = {
                id: Date.now() + '_ai_error',
                from: 'ai', 
                text: errorMessage,
                timestamp: new Date().toISOString(),
                isError: true
            };
            
            chat.messages.push(aiMessage);
        } finally {
            this.hideTypingIndicator();
            this.saveChats();
            this.renderMessages();
            this.isWaitingForResponse = false;
            this.updateSendButton();
        }
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

    showDeleteAllConfirmation() {
        if (this.chats.length === 0) return;
        
        const confirmed = confirm(`Are you sure you want to delete all ${this.chats.length} chats? This action cannot be undone.`);
        
        if (confirmed) {
            this.deleteAllChats();
        }
    }

    deleteAllChats() {
        this.chats = [];
        this.currentChat = null;
        this.editingMessageId = null;
        
        this.saveChats();
        this.renderChats();
        this.showWelcomeScreen();
        
        this.elements.messageInput.value = '';
        this.clearFile();
        
        this.showNotification('All chats deleted successfully', 'success');
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
        
        this.cancelEdit(); // Cancel any ongoing edits
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
            id: Date.now() + '_user',
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
                id: Date.now() + '_ai',
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
                id: Date.now() + '_ai_error',
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
        const timeoutId = setTimeout(() => controller.abort(), 30000); // Increased timeout to 30s
        
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
                    signal: controller.signal,
                    headers: {
                        'Accept': 'application/json, text/plain, */*'
                    }
                });
            } else {
                response = await fetch(webhookUrl, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json, text/plain, */*'
                    },
                    body: JSON.stringify({ message: text }),
                    mode: 'cors',
                    signal: controller.signal
                });
            }
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status} - ${response.statusText}`);
            }
            
            const contentType = response.headers.get('content-type') || '';
            let aiResponse;
            
            if (contentType.includes('application/json')) {
                const data = await response.json();
                console.log('Webhook response:', JSON.stringify(data, null, 2));
                
                // Enhanced response parsing for better n8n compatibility
                aiResponse = data.reply || 
                           data.message || 
                           data.response || 
                           data.output || 
                           data.result ||
                           data.data?.message || 
                           data.data?.response || 
                           data.data?.output ||
                           data.data?.result ||
                           (typeof data === 'string' ? data : JSON.stringify(data, null, 2));
            } else {
                aiResponse = await response.text();
            }
            
            if (!aiResponse || !aiResponse.trim()) {
                aiResponse = 'I received your message but got an empty response. Please try rephrasing your question or check if the AI service is working properly.';
            }
            
            return { message: aiResponse };
            
        } catch (error) {
            clearTimeout(timeoutId);
            
            // Enhanced error handling
            if (error.name === 'AbortError') {
                throw new Error('Request timed out. The AI service may be busy. Please try again.');
            }
            
            throw error;
        }
    }

    getErrorMessage(error, hasFile = false) {
        const errorMsg = error.message;
        
        if (errorMsg.includes('timed out') || errorMsg.includes('AbortError')) {
            return `**⏱️ Request Timeout**

The AI service is taking longer than expected to respond.

**Solutions:**
- Try again in a few moments
- If uploading a file, ensure it's not too large
- Check your internet connection
- The n8n service might be busy processing other requests

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('CORS') || errorMsg.includes('Failed to fetch')) {
            return `**⚠️ Connection Issue**

Unable to connect to the AI service due to a network error.

**Solutions:**
- Check your internet connection
- Ensure the n8n webhook is accessible and has proper CORS headers
- Try refreshing the page and attempting again
- Contact support if the issue persists

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('500')) {
            return `**⚠️ Server Error**

The AI service encountered an internal error while processing your request.

**Solutions:**
- Try again in a few moments
- If uploading a file, ensure it's a valid PDF, DOCX, RTF, or TXT file
- Check the n8n workflow logs for detailed error information
- Verify all workflow nodes are properly configured

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('404')) {
            return `**🔍 Service Not Found**

The AI service endpoint could not be found.

**Solutions:**
- Verify the webhook URL is correct
- Ensure the n8n workflow is active and published
- Check if the webhook path has changed

**Error:** ${errorMsg}`;
        }
        
        if (errorMsg.includes('400')) {
            return `**📝 Invalid Request**

The request format was not accepted by the AI service.

**Solutions:**
- Try rephrasing your question
- If uploading a file, ensure it's in a supported format
- Check that the file is not corrupted

**Error:** ${errorMsg}`;
        }
        
        return `**❌ Unexpected Error**

An error occurred while processing your request.

**Solutions:**
- Try refreshing the page and attempting again
- Check the browser console for additional details
- Ensure your internet connection is stable
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
        this.cancelEdit(); // Cancel any ongoing edits when closing
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
    if (juriAI.editingMessageId) {
        juriAI.saveEditedMessage();
    } else {
        juriAI.sendMessage();
    }
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