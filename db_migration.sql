-- SQL migration script to add missing columns and create audit_logs table.
-- Run this as a database user with ALTER and CREATE privileges (e.g. root).

-- Add distance to rentedcars if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='rentedcars' AND COLUMN_NAME='distance');
SET @s = IF(@col_exists=0,'ALTER TABLE rentedcars ADD COLUMN distance DOUBLE DEFAULT 0','SELECT "distance already exists"');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add no_of_days to rentedcars if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='rentedcars' AND COLUMN_NAME='no_of_days');
SET @s = IF(@col_exists=0,'ALTER TABLE rentedcars ADD COLUMN no_of_days INT DEFAULT 0','SELECT "no_of_days already exists"');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add total_amount to rentedcars if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='rentedcars' AND COLUMN_NAME='total_amount');
SET @s = IF(@col_exists=0,'ALTER TABLE rentedcars ADD COLUMN total_amount DOUBLE DEFAULT 0','SELECT "total_amount already exists"');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add booked_until to cars if missing
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='cars' AND COLUMN_NAME='booked_until');
SET @s = IF(@col_exists=0,'ALTER TABLE cars ADD COLUMN booked_until DATE DEFAULT NULL','SELECT "booked_until already exists"');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add booking_status to rentedcars if missing (pending/approved/rejected)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='rentedcars' AND COLUMN_NAME='booking_status');
SET @s = IF(@col_exists=0,'ALTER TABLE rentedcars ADD COLUMN booking_status VARCHAR(20) DEFAULT ''pending''','SELECT "booking_status already exists"');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add is_admin to userdetails if missing (0 = client, 1 = admin)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='userdetails' AND COLUMN_NAME='is_admin');
SET @s = IF(@col_exists=0,'ALTER TABLE userdetails ADD COLUMN is_admin TINYINT(1) DEFAULT 0','SELECT "is_admin already exists"');
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create audit_logs table if missing
CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_email VARCHAR(255),
  booking_id INT,
  action VARCHAR(50),
  note TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: Backfill total_amount from fare where empty
UPDATE rentedcars SET total_amount = fare WHERE (total_amount IS NULL OR total_amount = 0) AND (fare IS NOT NULL AND fare > 0);

-- Done
SELECT 'Migration finished' as status;
