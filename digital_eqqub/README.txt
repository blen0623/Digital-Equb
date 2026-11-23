=====================================================
PROJECT NAME: DIGITAL EQQUB SYSTEM
DEVELOPED BY: [Group Members]
DATE: December 2025
=====================================================

1. PROJECT OVERVIEW
-------------------
Digital Eqqub is a web-based saving and credit platform that 
allows users to register, manage their profiles, and participate 
in Eqqub groups digitally.

2. SETUP INSTRUCTIONS
---------------------
To run this project on your local machine, follow these steps:

Step 1: Copy the "digital_eqqub" folder into your XAMPP "htdocs" directory.
Step 2: Open XAMPP Control Panel and start Apache and MySQL.
Step 3: Go to phpMyAdmin (localhost/phpmyadmin).
Step 4: Create a new database named: digital_eqqub_db
Step 5: Import the "digital_eqqub_db.sql" file provided in the folder.
Step 6: Access the system in your browser: http://localhost/digital_eqqub

3. IMPORTANT NOTE ON DATABASE
-----------------------------
The database tables (users and eqqub_groups) have been cleared 
(Truncated) for a clean installation. 

*** PLEASE REGISTER A NEW ACCOUNT FIRST *** 
Since the database is empty, use the "Create New Account" link 
on the login page to get started.

4. CORE FEATURES INCLUDED
-------------------------
* User Authentication: Login and Registration system.
* Dashboard: Overview of user statistics and active groups.
* Dynamic Profile: 
    - Full-screen background customization.
    - User details fetched dynamically from MySQL.
    - Profile picture upload and update functionality.
* Profile Management: Users can edit their Name, Email, and Phone number.
* Clean UI: Modern Glassmorphism design using CSS.

5. FOLDER STRUCTURE
-------------------
* /uploads   - Stores user-uploaded profile pictures.
* db.php     - Database connection configuration.
* profile.php - View user profile.
* edit_profile.php - Update user profile information.
* login.php & register.php - Authentication pages.

=====================================================
THANK YOU!
=====================================================