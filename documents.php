<?php
include_once 'header.php';
require_once 'config/database.php';
require_once 'classes/Document.php';

$database = new Database();
$db = $database->getConnection();
$document = new Document($db);

$user_documents = $document->getUserDocuments($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';
?>


    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Documents</h1>
                    <p class="text-gray-600 mt-1">Manage your uploaded documents</p>
                </div>
                <button onclick="openUploadModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Upload Document
                </button>
            </div>
        </div>

        <!-- Documents Content -->
        <div class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo count($user_documents); ?></p>
                            <p class="text-gray-600 text-sm">Total Documents</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo count(array_filter($user_documents, function($doc) { return $doc['ai_processed']; })); ?></p>
                            <p class="text-gray-600 text-sm">AI Processed</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo count(array_filter($user_documents, function($doc) { return $doc['status'] === 'processing'; })); ?></p>
                            <p class="text-gray-600 text-sm">Processing</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-database text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-800"><?php echo array_sum(array_column($user_documents, 'file_size')); ?></p>
                            <p class="text-gray-600 text-sm">Total Size (bytes)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Table -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Your Documents</h2>
                        <div class="flex space-x-3">
                            <select class="border rounded-lg px-3 py-1 text-sm">
                                <option>All Status</option>
                                <option>Uploaded</option>
                                <option>Processing</option>
                                <option>Processed</option>
                            </select>
                            <div class="relative">
                                <input type="text" placeholder="Search documents..." class="pl-8 pr-4 py-1 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search absolute left-2 top-2 text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (empty($user_documents)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No documents uploaded</h3>
                        <p class="text-gray-500 mb-4">Start by uploading your first document</p>
                        <button onclick="openUploadModal()" class="text-blue-600 hover:text-blue-700 font-medium">
                            <i class="fas fa-plus mr-2"></i>Upload Document
                        </button>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AI Processed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($user_documents as $doc): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-file-alt text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($doc['original_name']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($doc['mime_type']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo number_format($doc['file_size'] / 1024, 1); ?> KB
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $doc['status'] === 'uploaded' ? 'bg-green-100 text-green-800' : 
                                                            ($doc['status'] === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                                <?php echo ucfirst($doc['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($doc['ai_processed']): ?>
                                                <i class="fas fa-check-circle text-green-500"></i>
                                            <?php else: ?>
                                                <i class="fas fa-clock text-yellow-500"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M j, Y', strtotime($doc['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="downloadDocument(<?php echo $doc['id']; ?>)" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button onclick="viewDocument(<?php echo $doc['id']; ?>)" class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button onclick="deleteDocument(<?php echo $doc['id']; ?>)" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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

    function toggleSubmenu(id) {
        const submenu = document.getElementById(id + '-submenu');
        submenu.classList.toggle('hidden');
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
        formData.append('user_id', <?php echo json_encode($_SESSION['user_id']); ?>);
        formData.append('timestamp', new Date().toISOString());

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
                // Refresh page to show new documents
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

    function downloadDocument(id) {
        window.open(`api/download.php?id=${id}`, '_blank');
    }

    function viewDocument(id) {
        window.open(`api/view.php?id=${id}`, '_blank');
    }

    function deleteDocument(id) {
        if (confirm('Are you sure you want to delete this document?')) {
            $.ajax({
                url: 'api/delete.php',
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    alert('Document deleted successfully!');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error deleting document: ' + error);
                }
            });
        }
    }
</script>
