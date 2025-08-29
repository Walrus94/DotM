# Gazelle Fork Refactoring Plan

## Project Overview
Transform Gazelle BitTorrent tracker into a modern music label website with streaming platform integration.

## Critical Requirements
- **NEVER modify or delete admin account 'Walrus' from database under any circumstances**
- Preserve all core site functionality during each phase
- Test thoroughly after each feature removal before proceeding
- Maintain community features (forums, comments, PMs)

---

## Milestone 1: Foundation Cleanup (Low Risk)

### Issue 1.1: Remove Bonus Points System
**Title:** `Remove Bonus Points System`
**Description:** Remove the entire bonus points system from Gazelle as it's not needed for a music label website.
**Priority:** 2 (Medium)
**Labels:** `milestone-1`, `low-risk`, `removal`
**Estimate:** 8 hours
**Team:** DOTM

**Sub-issues to create:**
- Remove `sections/bonus/` directory
- Remove `app/BonusPool.php` class
- Remove `app/User/Bonus.php` class
- Drop `bonus_pool` table
- Drop `bonus_pool_contrib` table
- Remove `BonusPoints` column from `users_main` table
- Update user display to hide bonus-related UI

**Branch:** `refactor/milestone-1/remove-bonus-system`
**Critical:** Test site functionality after removal before proceeding.

---

### Issue 1.2: Remove Ratio System
**Title:** `Remove Ratio System`
**Description:** Remove the ratio system and upload/download tracking as it's not needed for streaming platforms.
**Priority:** 2 (Medium)
**Labels:** `milestone-1`, `low-risk`, `removal`
**Estimate:** 6 hours
**Team:** DOTM

**Sub-issues to create:**
- Remove `RequiredRatio` column from `users_main`
- Remove `Uploaded`/`Downloaded` columns from `users_main`
- Remove ratio functions from `lib/util.php`
- Update user profiles to hide ratio stats
- Remove ratio display from top10 and user pages

**Branch:** `refactor/milestone-1/remove-ratio-system`
**Critical:** Test site functionality after removal before proceeding.

---

### Issue 1.3: Remove Requests System
**Title:** `Remove Requests System`
**Description:** Remove the music requests system completely as it's not needed for a music label website.
**Priority:** 2 (Medium)
**Labels:** `milestone-1`, `low-risk`, `removal`
**Estimate:** 4 hours
**Team:** DOTM

**Sub-issues to create:**
- Remove `sections/requests/` directory
- Remove `app/Request.php` class
- Drop `requests` table
- Drop `requests_votes` table
- Drop `requests_comments` table
- Remove request-related navigation and UI

**Branch:** `refactor/milestone-1/remove-requests-system`
**Critical:** Test site functionality after removal before proceeding.

---

### Issue 1.4: Remove Reports System
**Title:** `Remove Reports System`
**Description:** Remove the content reporting system as it's not needed for a music label website.
**Priority:** 2 (Medium)
**Labels:** `milestone-1`, `low-risk`, `removal`
**Estimate:** 4 hours
**Team:** DOTM

**Sub-issues to create:**
- Remove `sections/reports/` directory
- Remove `sections/reportsv2/` directory
- Remove `app/Report.php` class
- Drop `reports` table
- Drop `reports_comments` table
- Drop `reports_votes` table
- Remove report-related UI and navigation

**Branch:** `refactor/milestone-1/remove-reports-system`
**Critical:** Test site functionality after removal before proceeding.

---

## Milestone 2: User System Modernization (Medium Risk)

### Issue 2.1: Replace Invite System with Free Registration
**Title:** `Replace Invite System with Free Registration`
**Description:** Replace the invite-only registration with free registration to allow open access to the music label website.
**Priority:** 1 (High)
**Labels:** `milestone-2`, `medium-risk`, `modification`
**Estimate:** 6 hours
**Team:** DOTM

**Sub-issues to create:**
- Remove `sections/user/invite.php`
- Remove `sections/user/invite_handle.php`
- Remove `Invites` and `Invites_Given` columns from `users_main`
- Enable `OPEN_REGISTRATION` in `lib/config.php`
- Test registration flow thoroughly
- Update registration UI to remove invite requirements

**Branch:** `refactor/milestone-2/modernize-registration`
**Critical:** Test registration thoroughly before proceeding.

---

### Issue 2.2: Modify Top10 System for Streaming
**Title:** `Modify Top10 System for Streaming`
**Description:** Update the top10 system to work with streaming metrics instead of torrent metrics.
**Priority:** 1 (High)
**Labels:** `milestone-2`, `medium-risk`, `modification`
**Estimate:** 8 hours
**Team:** DOTM

**Sub-issues to create:**
- Update `sections/top10/torrents.php` for streaming data
- Modify `app/Top10/Torrent.php` for streaming metrics
- Replace torrent metrics (snatches, seeders) with streaming metrics
- Keep time-based rankings (day, week, month, year, overall)
- Test top10 functionality with streaming data

**Branch:** `refactor/milestone-2/modify-top10`
**Critical:** Ensure top10 still displays correctly after changes.

---

## Milestone 3: Core System Transformation (High Risk)

### Issue 3.1: Replace Torrent System with Streaming
**Title:** `Replace Torrent System with Streaming`
**Description:** Replace the core torrent system with a streaming-based music library. This is the highest risk change.
**Priority:** 0 (Urgent)
**Labels:** `milestone-3`, `high-risk`, `replacement`
**Estimate:** 16 hours
**Team:** DOTM

**Sub-issues to create:**
- Remove `sections/torrents/` directory
- Remove `app/Torrent.php` class
- Remove `app/Tracker.php` class
- Create new `sections/streaming/` directory
- Create new `app/StreamingRelease.php` class
- Modify `app/TGroup.php` to work with streaming
- Drop torrent-related database tables
- Create streaming-related database tables
- Test core music cataloging functionality

**Branch:** `refactor/milestone-3/replace-torrents-streaming`
**Critical:** This is the highest risk change. Test extensively before proceeding.

---

### Issue 3.2: Update Search and Browse System
**Title:** `Update Search and Browse System`
**Description:** Modify the search and browse system to work with streaming data instead of torrent data.
**Priority:** 1 (High)
**Labels:** `milestone-3`, `high-risk`, `modification`
**Estimate:** 10 hours
**Team:** DOTM

**Sub-issues to create:**
- Update search indexing for streaming data
- Modify browse functionality for streaming releases
- Maintain advanced filtering capabilities (artist, year, format)
- Update search result caching
- Test search and browse functionality

**Branch:** `refactor/milestone-3/update-search-browse`
**Critical:** Ensure search and browse still work correctly after changes.

---

## Milestone 4: Final Integration & Testing

### Issue 4.1: System Integration Testing
**Title:** `System Integration Testing`
**Description:** Comprehensive testing of the refactored system to ensure all functionality works correctly.
**Priority:** 1 (High)
**Labels:** `milestone-4`, `testing`
**Estimate:** 12 hours
**Team:** DOTM

**Sub-issues to create:**
- Test all streaming functionality
- Verify community features still work (forums, comments, PMs)
- Test admin functions (especially 'Walrus' account)
- Test user registration and authentication
- Test search and browse functionality
- Performance testing of streaming queries

**Branch:** `refactor/milestone-4/integration-testing`
**Critical:** Ensure all core functionality works before going live.

---

### Issue 4.2: Performance Optimization
**Title:** `Performance Optimization`
**Description:** Optimize the streaming system for performance and ensure it meets requirements.
**Priority:** 2 (Medium)
**Labels:** `milestone-4`, `optimization`
**Estimate:** 8 hours
**Team:** DOTM

**Sub-issues to create:**
- Optimize streaming data queries
- Update caching strategies for streaming data
- Database query optimization
- Load testing of streaming features
- Performance monitoring setup

**Branch:** `refactor/milestone-4/performance-optimization`
**Critical:** Ensure site performance meets requirements.

---

## Branching Strategy

### Main Branches
- **Main branch**: Never modify directly
- **Milestone branches**: `refactor/milestone-1`, `refactor/milestone-2`, `refactor/milestone-3`, `refactor/milestone-4`

### Feature Branches
- **Feature branches**: Branch from milestone branch (e.g., `refactor/milestone-1/remove-bonus-system`)
- **Sub-issue branches**: Branch from feature branch (e.g., `refactor/milestone-1/remove-bonus-system/drop-bonus-tables`)

---

## Risk Assessment

### Low Risk (Milestone 1)
- Bonus points, ratio, requests, reports removal
- These features are not core to site functionality
- Easy to test and verify

### Medium Risk (Milestone 2)
- User registration changes
- Top10 system modifications
- Requires thorough testing

### High Risk (Milestone 3)
- Core torrent system replacement
- Database schema changes
- Extensive testing required

### Testing (Milestone 4)
- Comprehensive validation
- Performance verification
- Final quality assurance

---

## Success Criteria

1. **Site remains functional** after each phase
2. **No broken links or references**
3. **User experience remains smooth**
4. **Admin functionality intact**
5. **Core music cataloging preserved**
6. **Community features working**
7. **Performance meets requirements**

---

## Notes for Background Agent

- Use the exact titles, descriptions, and metadata provided
- Create issues in the correct milestone order
- Set appropriate priorities, labels, and estimates
- Ensure all critical notes are included in descriptions
- Test the Linear integration with a simple issue first
- Verify all issues are created successfully before proceeding
