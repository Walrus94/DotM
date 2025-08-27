# Gazelle to Music Label Catalog - Simplified Fresh Start Plan

## Project Overview
Transform Gazelle into a clean music label catalog with streaming platform integration. Starting fresh with no data migration, targeting <100 releases initially, supporting Spotify, Apple Music, Bandcamp, and SoundCloud.

**Key Simplifications:**
- No complex data migration (fresh start)
- Small catalog optimization
- Focus on 4 streaming platforms
- Clean, modern implementation

---

## Phase 1: Clean Slate Foundation (Week 1)
**Estimated: 5-7 days**

### Day 1-2: Database Cleanup
- [ ] **Create new branch** `music-catalog-fresh` from master
- [ ] **Drop all BitTorrent tables** 
  ```sql
  DROP TABLE IF EXISTS torrents, torrents_group, torrents_artists, 
                       torrents_tags, torrents_leech_stats, xbt_*,
                       bonus_*, users_freeleeches, users_downloads, etc.
  ```
- [ ] **Create minimal release schema**
  ```sql
  CREATE TABLE release (
    ID int PRIMARY KEY AUTO_INCREMENT,
    Name varchar(300) NOT NULL,
    Year smallint,
    record_label varchar(200),
    catalog_number varchar(100),
    release_type tinyint,
    WikiBody text,
    WikiImage varchar(500),
    showcase boolean DEFAULT 0,
    created datetime DEFAULT CURRENT_TIMESTAMP,
    updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  );
  
  CREATE TABLE release_platform (
    ID int PRIMARY KEY AUTO_INCREMENT,
    ReleaseID int NOT NULL,
    Platform enum('Spotify', 'Apple Music', 'Bandcamp', 'SoundCloud'),
    Url varchar(500) NOT NULL,
    created datetime DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ReleaseID) REFERENCES release(ID) ON DELETE CASCADE
  );
  ```
- [ ] **Simplify user tables** - Remove ratio, uploaded, downloaded, bonus columns
- [ ] **Keep essential tables** - artists, tags, forums, comments, users_main

### Day 3: Code Structure Cleanup  
- [ ] **Remove BitTorrent sections entirely**
  ```bash
  rm -rf sections/bonus/
  rm -rf sections/better/
  rm -rf sections/logchecker/
  rm -rf sections/upload/
  ```
- [ ] **Remove BitTorrent classes**
  ```bash
  rm app/TorrentAbstract.php
  rm app/Torrent.php
  rm app/Manager/Torrent.php
  rm app/User/Bonus.php
  rm -rf app/Better/
  ```
- [ ] **Clean app/ directory** - Remove all torrent-related files

### Day 4-5: Basic Release System
- [ ] **Implement clean Release class**
  ```php
  class Release extends BaseObject {
    public function name(): string
    public function year(): ?int  
    public function platforms(): array
    public function addPlatform(string $platform, string $url): bool
  }
  ```
- [ ] **Create Release Manager**
- [ ] **Update TGroup to extend Release** (for compatibility during transition)
- [ ] **Basic release display page** - Show release with platform links

### Success Criteria:
- ✅ Clean database with no torrent tables
- ✅ Basic release entity functional
- ✅ Platform links working
- ✅ No BitTorrent code remains

---

## Phase 2: Core Music Catalog (Week 2-3)
**Estimated: 10-14 days**

### Week 2: Admin Content Management

#### Day 1-2: Admin Release Management
- [ ] **Create admin release form**
  ```html
  <form>
    <input name="name" placeholder="Album Name" required>
    <input name="year" type="number" placeholder="Year">
    <input name="record_label" placeholder="Record Label">
    <input name="catalog_number" placeholder="Catalog Number">
    <select name="release_type">
      <option>Album</option>
      <option>EP</option>
      <option>Single</option>
    </select>
    <textarea name="description" placeholder="Description"></textarea>
    <input name="cover_image" placeholder="Cover Image URL">
  </form>
  ```
- [ ] **Platform links management**
  ```html
  <div id="platforms">
    <input name="spotify_url" placeholder="Spotify URL">
    <input name="apple_music_url" placeholder="Apple Music URL">  
    <input name="bandcamp_url" placeholder="Bandcamp URL">
    <input name="soundcloud_url" placeholder="SoundCloud URL">
  </div>
  ```
- [ ] **Release list page for admins** - View, edit, delete releases

#### Day 3-4: Artist Integration
- [ ] **Preserve artist system** - Keep existing artist tables and classes
- [ ] **Update release-artist relationships**
  ```sql
  CREATE TABLE release_artist (
    release_id int NOT NULL,
    AliasID int NOT NULL,
    artist_role_id int NOT NULL,
    PRIMARY KEY (release_id, AliasID, artist_role_id),
    FOREIGN KEY (release_id) REFERENCES release(ID),
    FOREIGN KEY (AliasID) REFERENCES artists_alias(AliasID)
  );
  ```
- [ ] **Update artist pages** - Show releases instead of torrents
- [ ] **Artist management** - Add/edit artists for releases

#### Day 5: Tag System
- [ ] **Preserve tag system** - Keep existing tag infrastructure
- [ ] **Update for releases**
  ```sql
  CREATE TABLE release_tag (
    TagID int NOT NULL,
    release_id int NOT NULL,
    PositiveVotes int DEFAULT 1,
    NegativeVotes int DEFAULT 0,
    UserID int NOT NULL,
    PRIMARY KEY (TagID, release_id),
    FOREIGN KEY (release_id) REFERENCES release(ID)
  );
  ```
- [ ] **Tag management interface** - Add/remove tags from releases

### Week 3: Search & Discovery

#### Day 1-2: Basic Search
- [ ] **Simple release search**
  ```php
  class ReleaseSearch {
    public function search(string $query): array {
      return $this->db->query(
        "SELECT r.*, GROUP_CONCAT(aa.Name) as artists
         FROM release r
         LEFT JOIN release_artist ra ON r.ID = ra.release_id  
         LEFT JOIN artists_alias aa ON ra.AliasID = aa.AliasID
         WHERE r.Name LIKE ? OR aa.Name LIKE ?
         GROUP BY r.ID",
        ["%$query%", "%$query%"]
      );
    }
  }
  ```
- [ ] **Search results page** - Display releases with platform links
- [ ] **Browse by year, genre, label** - Basic filtering

#### Day 3-4: Release Pages
- [ ] **Individual release pages**
  - Cover art display
  - Release information (year, label, catalog number)
  - Artist credits with roles
  - Platform streaming links
  - Tags and genre information
  - Description/wiki content
- [ ] **Release listing pages** - Browse all releases
- [ ] **Artist discography pages** - All releases by artist

#### Day 5: Platform Integration Polish
- [ ] **Platform link validation** - Check URL formats
- [ ] **Platform icons/styling** - Visual indicators for each platform
- [ ] **External link handling** - Open in new tabs, tracking

### Success Criteria:
- ✅ Complete admin interface for release management
- ✅ Artist-release relationships working
- ✅ Basic search and browse functionality
- ✅ Clean release detail pages with platform links

---

## Phase 3: Community & Polish (Week 4)
**Estimated: 5-7 days**

### Day 1-2: User System Simplification
- [ ] **Preserve user registration** - Keep existing system, remove ratio tracking
- [ ] **Clean user profiles** - Remove upload/download stats, keep join date, forum posts
- [ ] **Admin-only content permissions** - Only admins can add/edit releases
- [ ] **Remove ratio/stats displays** - Clean user pages of BitTorrent elements

### Day 3: Community Features (Preserve Existing)
- [ ] **Keep forum system intact** - No changes to existing forum functionality
- [ ] **Keep private messaging** - Preserve existing PM system
- [ ] **Keep donation system** - Preserve for label support
- [ ] **Update comments for releases** - Adapt existing comment system to work with releases
- [ ] **Keep user social features** - Friends, subscriptions, etc.

### Day 4-5: Frontend Polish
- [ ] **Keep classic Gazelle PostMod design** - Preserve existing CSS/styling
- [ ] **Navigation cleanup** - Remove torrent-related menu items only
- [ ] **Homepage adaptation** - Replace torrent features with release features
- [ ] **Release page styling** - Adapt existing torrent group pages for releases

### Success Criteria:
- ✅ Clean user system without BitTorrent elements
- ✅ Community features (forums, comments) working
- ✅ Modern, responsive design
- ✅ Intuitive navigation for music discovery

---

## Phase 4: Advanced Features (Optional - Week 5+)
**If time permits and feedback is positive**

### Content Discovery
- [ ] **Collage system** - Curated release collections
- [ ] **Release recommendations** - "If you like this, try..."
- [ ] **New release notifications** - Email/RSS feeds
- [ ] **Genre exploration** - Browse by genre with statistics

### Enhanced Admin Tools  
- [ ] **Bulk import** - CSV upload for multiple releases
- [ ] **Platform URL detection** - Auto-detect platform from URLs
- [ ] **Duplicate detection** - Prevent duplicate releases
- [ ] **Analytics dashboard** - Usage statistics

### API & Integration
- [ ] **REST API** - JSON endpoints for mobile apps
- [ ] **Import from Discogs/MusicBrainz** - Auto-populate release data
- [ ] **Export functionality** - Backup catalog data
- [ ] **Webhook integration** - Notify external services of new releases

---

## Technical Implementation Details

### Database Schema (Minimal)
```sql
-- Core tables (simplified)
release (ID, Name, Year, record_label, catalog_number, release_type, WikiBody, WikiImage, showcase, created, updated)
release_platform (ID, ReleaseID, Platform, Url, created)  
release_artist (release_id, AliasID, artist_role_id)
release_tag (TagID, release_id, PositiveVotes, NegativeVotes, UserID)

-- Preserved from existing
artists_group (ArtistID, RevisionID, PrimaryAlias)
artists_alias (AliasID, ArtistID, Name, Redirect, UserID)
artist_role (artist_role_id, name, title)
tags (ID, Name, TagType, Uses, UserID)
users_main (ID, Username, Email, PassHash, PermissionID, created)
forums (existing forum tables)
comments (existing comment tables, adapt Page field for releases)
```

### Key Classes
```php
// Core entities
app/Release.php - Main release entity
app/ReleasePlatform.php - Streaming platform links
app/Manager/Release.php - Release management
app/Manager/ReleasePlatform.php - Platform management

// Preserved/adapted
app/Artist.php - Keep existing artist system
app/Tag.php - Keep existing tag system  
app/User.php - Simplified user system
app/Forum*.php - Keep forum system
app/Comment/ - Adapt for releases

// New sections
sections/releases/ - Browse and search releases
sections/admin/releases/ - Admin release management
```

### URL Structure
```
/releases/ - Browse all releases
/releases/search?q=query - Search releases  
/releases/{id} - Individual release page
/artists/{id} - Artist page with discography
/admin/releases/ - Admin release management
/admin/releases/add - Add new release
/admin/releases/{id}/edit - Edit release
```

---

## Risk Assessment & Mitigation

### Low Risk (Fresh Start Benefits)
- **No data migration** - Can't break existing data
- **Small catalog** - Performance not critical initially  
- **Simple features** - Less complexity, fewer bugs

### Medium Risk Areas
- **Artist system integration** - Ensure artist-release relationships work correctly
- **Search functionality** - Must be intuitive for music discovery
- **Platform URL validation** - Ensure links work correctly

### Mitigation Strategies
- **Incremental testing** - Test each component thoroughly
- **Backup branch strategy** - Easy rollback if needed
- **User feedback integration** - Get early feedback on usability

---

## Success Metrics

### Technical Metrics  
- [ ] Zero BitTorrent functionality remains
- [ ] All 4 streaming platforms supported
- [ ] Release pages load in <1 second
- [ ] Search returns results in <500ms
- [ ] Mobile responsive on all major devices

### User Experience Metrics
- [ ] Intuitive release discovery
- [ ] Easy admin content management  
- [ ] Clean, modern interface
- [ ] Functional community features (forums, comments)

### Content Metrics
- [ ] Admin can add releases in <2 minutes
- [ ] Platform links work 100% of the time
- [ ] Search finds relevant releases accurately
- [ ] Artist pages show complete discographies

---

## Timeline Summary

**Total Duration:** 4 weeks (with optional 5th week for advanced features)

**Week 1:** Clean slate - remove BitTorrent, create release foundation
**Week 2:** Admin tools - release management, artist integration, tags  
**Week 3:** Search & discovery - release pages, search, browsing
**Week 4:** Community & polish - users, forums, frontend design
**Week 5+:** Advanced features - collages, APIs, bulk import (optional)

**Resource Requirement:** 1 developer full-time (me!)

This simplified approach leverages the fresh start to create a much cleaner, faster implementation focused specifically on music catalog needs.