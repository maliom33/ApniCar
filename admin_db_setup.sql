-- Create admin table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Add is_admin column to userdetails if it doesn't exist (for backward compatibility)
ALTER TABLE userdetails ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) DEFAULT 0;