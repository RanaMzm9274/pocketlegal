# PocketLegal Dashboard

A modern legal document management dashboard built with PHP, Tailwind CSS, and JavaScript/jQuery.

## Features

- **User Authentication**: Secure login/logout system with session management
- **AI Contract Review**: Advanced AI-powered contract analysis with n8n webhook integration
- **Document Upload**: Drag-and-drop file upload with database storage
- **Responsive Design**: Modern UI built with Tailwind CSS
- **Dashboard Analytics**: Contract workflow tracking and task management
- **Error Handling**: Comprehensive error handling and logging
- **Security**: XSS protection, CSRF prevention, and secure file uploads

## Setup Instructions

### 1. Database Setup

1. Create a MySQL database named `pocketlaw`
2. Import the schema from `database/schema.sql`
3. Update database credentials in `config/database.php`

### 2. File Permissions

Make sure the following directories are writable:
```bash
chmod 755 uploads/
chmod 755 api/
```

### 3. Webhook Configuration

The AI Contract Review feature uses n8n webhooks for processing:
- **AI Query**: `https://n8n.srv909751.hstgr.cloud/webhook/query`
- **Document Upload**: `https://n8n.srv909751.hstgr.cloud/webhook/doc_upload`

Configure your webhook URLs in the `.env` file (copy from `.env.example`).

### 4. Demo Login

Use these credentials to test the application:
- **Email**: user@pocketlegal.com
- **Password**: password123

## File Structure

```
├── index.php              # Main dashboard
├── login.php              # Login page
├── logout.php             # Logout handler
├── config/
│   └── database.php       # Database configuration
├── classes/
│   ├── User.php          # User management class
│   └── Document.php      # Document management class
├── api/
│   ├── upload.php        # File upload API
│   └── ai_query.php      # AI query API
├── database/
│   └── schema.sql        # Database schema
└── uploads/              # File upload directory
```

## Features Implementation

### ✅ Implemented
- User authentication system
- Dashboard UI matching the design
- AI contract review with advanced webhook integration
- Document upload with drag-and-drop support
- Responsive sidebar navigation
- File management system
- Error handling and logging
- Security improvements
- Database auto-initialization

### 🔄 Planned (Next Phase)
- Create document functionality
- eSignature integration
- Advanced task management
- Document workflow tracking
- User management system

## API Endpoints

### POST /api/ai_contract_review.php
AI contract review with file upload
```json
{
  "instructions": "Review this contract for potential risks",
  "document": "file_upload",
  "conversation_id": "optional"
}
```

### POST /api/ai_query.php
Simple AI queries without file upload

### POST /api/upload.php
Upload documents (multipart/form-data)
- Supports multiple file uploads
- Automatic webhook notification
- Database logging

## Security Features

- Session-based authentication
- SQL injection protection with PDO
- File upload validation
- XSS protection headers
- Input sanitization
- Error logging
- Secure file handling

## Browser Support

- Chrome 60+
- Firefox 60+
- Safari 12+
- Edge 79+

## Environment Variables

Copy `.env.example` to `.env` and configure:
- Database credentials
- n8n webhook URLs
- File upload settings
- Security settings

## License

MIT License - feel free to use this for your projects!