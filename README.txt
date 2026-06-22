========================================
  ONLINE VOTING SYSTEM - Setup Guide
  Built with PHP 7+ and MySQL (XAMPP)
========================================

STEP 1: INSTALL XAMPP
- Download from: https://www.apachefriends.org
- Install and open XAMPP Control Panel
- Start APACHE and MYSQL

STEP 2: COPY PROJECT FILES
- Copy the entire "voting_system" folder to:
  C:\xampp\htdocs\voting_system

STEP 3: IMPORT DATABASE
- Open browser → go to: http://localhost/phpmyadmin
- Click "New" → database name: voting_db → Click Create
- Click on "voting_db" → click "Import" tab
- Choose file: voting_system/sql/voting_db.sql
- Click "Go"

STEP 4: OPEN PROJECT
- Voter Portal: http://localhost/voting_system
- Admin Panel:  http://localhost/voting_system/admin

========================================
LOGIN CREDENTIALS
========================================

ADMIN:
  URL:      http://localhost/voting_system/admin
  Username: admin
  Password: admin123

SAMPLE VOTERS (all use password: voter123):
  Email: fayaz@gmail.com  | Voter ID: VOT001
  Email: priya@gmail.com  | Voter ID: VOT002
  Email: rahul@gmail.com  | Voter ID: VOT003

========================================
FEATURES
========================================
✅ Voter Registration & Login
✅ Vote for Multiple Positions
✅ Live Results Page (auto-refresh)
✅ Admin Dashboard with Stats
✅ Admin: Add/Edit/Delete Candidates
✅ Admin: Add/Edit/Delete Positions
✅ Admin: Block/Unblock Voters
✅ Admin: Reset Individual Voter's Vote
✅ Admin: Reset ALL Votes
✅ Admin: Open/Close Voting
✅ Admin: Change Password
✅ Duplicate Vote Prevention
✅ Works on PHP 7+ (no deprecated functions)

========================================
TROUBLESHOOTING
========================================
Q: Blank page or error?
A: Make sure XAMPP Apache + MySQL are running

Q: Database connection failed?
A: Import voting_db.sql in phpMyAdmin

Q: White screen on vote.php?
A: Make sure you are logged in as a voter first

========================================
