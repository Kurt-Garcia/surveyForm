# Database Seeders Documentation

This document provides information about the various database seeders available in the survey application.

## Available Seeders

### 1. DatabaseSeeder (Main Seeder)
**File:** `DatabaseSeeder.php`  
**Command:** `php artisan db:seed`

This is the main seeder that runs all other seeders in the correct order:
1. SbuAndSiteSeeder - Creates SBUs and Sites
2. AdminSeeder - Creates basic admin user
3. LargeDataSeeder - Creates bulk data for testing
4. DetailedSurveySeeder - Creates comprehensive surveys
5. SurveyResponseSeeder - Creates sample survey responses

### 2. LargeDataSeeder
**File:** `LargeDataSeeder.php`  
**Command:** `php artisan db:seed --class=LargeDataSeeder`

Creates extensive sample data:
- **20 Admin Users** with various roles and access levels
- **100 Regular Users** with realistic names and contact information
- **50 Surveys** with different titles and 5-15 questions each
- Assigns random SBUs and Sites to all users and surveys

### 3. DetailedSurveySeeder
**File:** `DetailedSurveySeeder.php`  
**Command:** `php artisan db:seed --class=DetailedSurveySeeder`

Creates 5 comprehensive, realistic surveys:
- **Employee Satisfaction Survey** (16 questions)
- **Customer Experience Feedback** (12 questions)
- **Training Program Effectiveness** (13 questions)
- **Workplace Safety Assessment** (12 questions)
- **Annual Performance Review** (15 questions)

Each survey includes various question types: text, textarea, radio, checkbox, select, number, email, date.

### 4. SurveyResponseSeeder
**File:** `SurveyResponseSeeder.php`  
**Command:** `php artisan db:seed --class=SurveyResponseSeeder`

Creates realistic survey responses:
- **10-30 responses per active survey**
- Intelligent response generation based on question content
- Realistic customer information (names, emails, phone numbers)
- 85% completion rate simulation

### 5. AdditionalAdminSeeder
**File:** `AdditionalAdminSeeder.php`  
**Command:** `php artisan db:seed --class=AdditionalAdminSeeder`

Creates specialized admin users with different access levels:
- **1 Super Administrator** - Full access to all SBUs and sites
- **3 Regional Managers** - Access to specific geographical regions
- **5 Department Heads** - Access to main sites across departments
- **Site Managers** - One manager per main site with sub-site access
- **4 Survey Specialists** - Full access for survey management

## Sample Data Generated

### Admin Users (Total: ~40+)
- Default admin (admin@gmail.com / admin123)
- 20 randomly generated admins
- 1 super administrator
- 3 regional managers
- 5 department heads
- Multiple site managers
- 4 survey specialists

### Regular Users (100+)
- Realistic names from common first/last name combinations
- Company email addresses
- Philippine mobile numbers
- Various status levels (active/inactive)
- Assigned to random SBUs and Sites

### Surveys (55+)
- 50 general surveys with random titles
- 5 detailed, comprehensive surveys
- Questions range from 5-16 per survey
- Various question types and complexity levels
- Realistic business scenarios

### Survey Responses (500-1500+)
- Intelligent responses based on question context
- Realistic customer data
- Varied completion rates
- Time-distributed response dates

## Usage Instructions

### Run All Seeders
```bash
php artisan db:seed
```

### Run Individual Seeders
```bash
# Large dataset for testing
php artisan db:seed --class=LargeDataSeeder

# Detailed surveys only
php artisan db:seed --class=DetailedSurveySeeder

# Survey responses only
php artisan db:seed --class=SurveyResponseSeeder

# Additional admin users only
php artisan db:seed --class=AdditionalAdminSeeder
```

### Reset and Reseed
```bash
# Reset database and run all seeders
php artisan migrate:fresh --seed

# Reset and run specific seeder
php artisan migrate:fresh
php artisan db:seed --class=SbuAndSiteSeeder
php artisan db:seed --class=LargeDataSeeder
```

## Default Credentials

### Admin Accounts
- **Default Admin:** admin@gmail.com / admin123
- **Super Admin:** superadmin@company.com / superadmin123
- **Regional Managers:** regional123
- **Department Heads:** dept123
- **Site Managers:** site123
- **Survey Specialists:** survey123

### User Accounts
- **Default User:** pirateKing@gmail.com / admin123
- **Generated Users:** user123 (for all generated users)

## Data Relationships

The seeders maintain proper relationships:
- **Admins** are assigned to multiple SBUs and Sites
- **Users** belong to SBUs and Sites
- **Surveys** are created by Admins and assigned to SBUs/Sites
- **Survey Questions** belong to Surveys
- **Survey Responses** link to Surveys and Questions

## Customization

### Modify Sample Data
Edit the arrays in each seeder file to customize:
- Names and email formats
- Survey titles and questions
- Response options and values
- User roles and permissions

### Adjust Quantities
Change the loop counters to generate more or fewer records:
- User count: Line ~75 in LargeDataSeeder.php
- Survey count: Line ~150 in LargeDataSeeder.php
- Response count: Line ~35 in SurveyResponseSeeder.php

### Add New Question Types
Extend the response generation logic in SurveyResponseSeeder.php to handle custom question types.

## Notes

- Run SbuAndSiteSeeder first to ensure SBUs and Sites exist
- LargeDataSeeder requires existing SBUs and Sites
- SurveyResponseSeeder requires existing active Surveys
- All seeders use Faker for realistic data generation
- The seeders are designed to be run multiple times safely (with some duplication)

## Troubleshooting

### Common Issues
1. **"SBUs and Sites not found"** - Run SbuAndSiteSeeder first
2. **"No active admins found"** - Ensure AdminSeeder has run successfully
3. **Memory issues** - Reduce the number of records being generated
4. **Duplicate emails** - The seeders may create duplicate emails if run multiple times

### Performance
- Large datasets may take several minutes to generate
- Consider running seeders individually for better control
- Monitor memory usage when generating many responses
