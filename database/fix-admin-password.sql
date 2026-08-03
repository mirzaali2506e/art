-- Fix admin password hash (run this if you already imported schema.sql)
-- Password: admin123
USE beadcraft_store;
UPDATE admin_users
SET password_hash = '$2y$12$Ut23DmeJrejef/DD5UnFPu1eVPR0V3J1slNoC3F579LWm3galPdxO'
WHERE username = 'admin';
