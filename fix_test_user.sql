-- Fix test user missing data that causes effectiveClassLevel error
-- First, ensure the user has proper permission data

-- Check if permission ID 1 exists and has proper data
INSERT IGNORE INTO permissions (ID, Name, Level, Secondary, PermittedForums, `Values`, StaffGroup, badge, DisplayStaff) 
VALUES (1, 'User', 0, 0, '', 'a:0:{}', NULL, '', 0);

-- Ensure the test user has proper user_summary entry (if missing)
INSERT IGNORE INTO user_summary (user_id, artist_added_total, collage_total, collage_contrib, download_total, download_unique, fl_token_total, forum_post_total, forum_thread_total, invited_total, leech_total, perfect_flac_total, perfecter_flac_total, request_bounty_total, request_bounty_size, request_created_total, request_created_size, request_vote_total, request_vote_size, seeding_total, seedtime_hour, snatch_total, snatch_unique, unique_group_total, upload_total)
SELECT 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
WHERE NOT EXISTS (SELECT 1 FROM user_summary WHERE user_id = 2);

-- Ensure the test user has proper users_info entry (if missing)
INSERT IGNORE INTO users_info (UserID, AdminComment, SiteOptions, RatioWatchEnds, RatioWatchDownload)
SELECT 2, 'Test user account for development', 'a:0:{}', NULL, 0
WHERE NOT EXISTS (SELECT 1 FROM users_info WHERE UserID = 2);

-- Ensure the test user has proper users_main entry with all required fields
UPDATE users_main SET 
    stylesheet_id = 1,
    auth_key = 'test_auth_key_123',
    torrent_pass = 'test_torrent_pass_123'
WHERE ID = 2;
