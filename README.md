# Scholarship Application Management System

A web-based scholarship application system built with PHP, MySQL, HTML, CSS and Bootstrap 5.
Final project for Web Systems Technologies (individual project).

## Features
- Students browse open scholarships and apply online
- Checks the GWA requirement, deadline and available slots
- Optional supporting document upload (PDF, JPG or PNG, up to 2 MB)
- Reference code to track the application status
- Staff login with a dashboard
- Staff can review, approve or reject applications and leave a message for the student
- Staff can edit and delete applications (CRUD)
- Staff can add, edit and delete scholarships (CRUD)
- Search and filter by name, scholarship or status
- Responsive Bootstrap 5 design

## Technologies
PHP, MySQL, HTML, CSS, Bootstrap 5, XAMPP, InfinityFree

## How to Run Locally
1. Copy the folder to `C:\xampp\htdocs\scholarship-system`
2. Start Apache and MySQL in XAMPP
3. In phpMyAdmin, create a database named `scholarship_db`
4. Import `database.sql`
5. Open merian.10001mb.com

## Staff Login
Open `/admin/login.php`
- Username: admin
- Password: admin123

## Database
Tables: `admins`, `scholarships`, `applications`
`applications` is linked to `scholarships` by a foreign key.
