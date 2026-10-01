-- FGCK Joyland: pastor portal appointment notifications
-- The existing notifications table already contains user_id and is_read.
-- Run this once if your existing installation was created from an older schema.

ALTER TABLE notifications
    ADD INDEX idx_notifications_user_read (user_id, is_read, created_at);
