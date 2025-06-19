# Developer Access Guide

## 🔒 Secret Developer Portal

This is a hidden developer portal with super-admin privileges that allows full control over the survey system.

### 🔗 Access Link

**Secret Developer Login URL:**
```
http://your-domain.com/secret-dev-access-fastdev-2025/login
```

For local development:
```
http://localhost/surveyForm/secret-dev-access-fastdev-2025/login
```

### 👤 Login Credentials

**Username:** `FastDev`  
**Email:** `jobgkurtkainne@gmail.com`  
**Password:** `Admin123`

> **Note:** You can login using either the username OR email address.

### 🚀 Setup Instructions

Before accessing the developer portal, you need to run the database migration and seeder:

1. **Run the migration to create the developers table:**
   ```bash
   php artisan migrate
   ```

2. **Seed the developer account:**
   ```bash
   php artisan db:seed --class=DeveloperSeeder
   ```

**⚡ Quick Access:**
[Secret Developer Login](http://localhost/surveyForm/secret-dev-access-FastDev-2025/login)

*This document should be kept confidential and only shared with authorized developers.*
