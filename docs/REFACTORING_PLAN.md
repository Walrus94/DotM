# Gazelle to Music Label Catalog - Refactoring Plan

## Project Overview
Transform Gazelle from a BitTorrent tracker to a static music label catalog with streaming platform integration, preserving excellent music organization features while eliminating all BitTorrent functionality.

---

## Epic 1: Project Foundation & Setup
**Priority: Critical** | **Estimated Points: 21**

### Tasks:
- [ ] **Setup new branch from master** - Create clean `music-catalog-refactor` branch
- [ ] **Update project documentation** - README, installation guides, remove BitTorrent references  
- [ ] **Configure development environment** - Remove tracker dependencies from Docker setup
- [ ] **Create target database schema** - Implement clean release-focused schema from `docs/plantuml/target.puml`
- [ ] **Setup migration framework** - Prepare for gradual data migration from torrent-centric to release-centric

**Success Criteria:**
- Clean development environment without BitTorrent dependencies
- Target database schema implemented
- Updated documentation reflecting music catalog purpose

---

## Epic 2: Core Data Model Migration  
**Priority: Critical** | **Estimated Points: 34**

### Tasks:
- [ ] **Implement Release entity** - Create core `Release` class with metadata (name, year, label, catalog number)
- [ ] **Implement Edition entity** - Create `Edition` class for different formats/remasters (without torrent file handling)
- [ ] **Implement ReleasePlatform entity** - Create streaming platform integration (Spotify, Apple Music, Bandcamp, etc.)
- [ ] **Migrate TGroup to Release** - Update existing `TGroup` references to use new `Release` structure
- [ ] **Update Artist relationships** - Migrate from torrent-artist to release-artist relationships
- [ ] **Update Tag system** - Migrate from torrent tags to release tags
- [ ] **Create migration scripts** - Scripts to migrate existing data from torrent tables to release tables

**Success Criteria:**
- Complete release-based data model
- All music metadata preserved and accessible
- Platform streaming links functional

---

## Epic 3: Search & Discovery System
**Priority: High** | **Estimated Points: 21**

### Tasks:
- [ ] **Implement release search** - Adapt search from torrents to releases with artist, year, label filters
- [ ] **Update Sphinx configuration** - Modify search indexes for release-focused content
- [ ] **Create release browse pages** - Replace torrent browsing with release browsing
- [ ] **Update AJAX search endpoints** - Modify API endpoints for release search
- [ ] **Implement platform filtering** - Allow filtering by streaming platform availability

**Success Criteria:**
- Fast, accurate release search functionality
- Browse pages show releases with platform links
- Search supports all relevant music metadata

---

## Epic 4: User Interface Transformation
**Priority: High** | **Estimated Points: 34**

### Tasks:
- [ ] **Update artist pages** - Show releases instead of torrents, include platform links
- [ ] **Create release detail pages** - Comprehensive release pages with editions and platform availability
- [ ] **Update collage system** - Migrate from torrent collages to release collages
- [ ] **Remove torrent indicators** - Remove seeding/leeching status, ratio displays
- [ ] **Update navigation** - Remove torrent upload, replace with content management
- [ ] **Create admin content management** - Interface for administrators to add/edit releases
- [ ] **Update mobile responsiveness** - Ensure new pages work on all devices

**Success Criteria:**
- Clean, modern UI focused on music discovery
- No BitTorrent terminology or functionality visible
- Admin tools for catalog management

---

## Epic 5: Community Features Preservation
**Priority: Medium** | **Estimated Points: 13**

### Tasks:
- [ ] **Preserve forum system** - Keep forums functional, remove torrent-related sections
- [ ] **Update comment system** - Migrate comments from torrents to releases
- [ ] **Preserve user profiles** - Keep user system, remove ratio/seeding statistics
- [ ] **Update notification system** - Adapt notifications for release updates instead of torrent events
- [ ] **Preserve donation system** - Keep donation functionality for label support

**Success Criteria:**
- Active community features without BitTorrent elements
- User engagement tools focused on music discovery
- Functional donation system

---

## Epic 6: Request System Adaptation
**Priority: Medium** | **Estimated Points: 21**

### Tasks:
- [ ] **Redesign request system** - Change from "request torrent" to "request release be added to catalog"
- [ ] **Remove bounty system** - Eliminate BitTorrent-based bounties
- [ ] **Update request workflow** - Admin fulfillment by adding releases to catalog
- [ ] **Preserve request voting** - Keep community voting on requests
- [ ] **Update request search** - Search for existing requests by music metadata

**Success Criteria:**
- Functional request system for catalog additions
- Community-driven content discovery
- Admin workflow for request fulfillment

---

## Epic 7: BitTorrent Elimination - Phase 1 (Safe Removals)
**Priority: High** | **Estimated Points: 13**

### Tasks:
- [ ] **Remove bonus point system** - Delete `sections/bonus/`, `app/User/Bonus.php`, bonus database tables
- [ ] **Remove better/transcoding system** - Delete `sections/better/`, transcoding request logic
- [ ] **Remove logchecker** - Delete `sections/logchecker/`, CD rip verification tools
- [ ] **Remove tracker integration** - Delete Ocelot communication, announce handling
- [ ] **Remove ratio enforcement** - Delete ratio watching, freeleech tokens

**Success Criteria:**
- BitTorrent economy systems completely removed
- No tracker communication functionality
- Simplified user system without ratio tracking

---

## Epic 8: BitTorrent Elimination - Phase 2 (Core Removal)
**Priority: High** | **Estimated Points: 21**

### Tasks:
- [ ] **Remove torrent file handling** - Delete upload/download torrent file functionality
- [ ] **Remove XBT tracker tables** - Drop all `xbt_*` database tables and related code
- [ ] **Remove peer tracking** - Delete seeders/leechers tracking, peer lists
- [ ] **Remove torrent statistics** - Delete seeding time, ratio calculations
- [ ] **Clean TorrentAbstract** - Remove or heavily refactor `app/TorrentAbstract.php`

**Success Criteria:**
- No torrent file functionality remains
- No peer-to-peer tracking
- Clean database without tracker tables

---

## Epic 9: Admin Tools & Content Management
**Priority: Medium** | **Estimated Points: 21**

### Tasks:
- [ ] **Create release management interface** - Admin tools to add/edit/delete releases
- [ ] **Implement bulk import tools** - Import releases from external sources (Discogs, MusicBrainz)
- [ ] **Create platform link management** - Tools to add/update streaming platform links
- [ ] **Implement content moderation** - Tools for reviewing and approving community submissions
- [ ] **Create backup/export functionality** - Export catalog data for backup

**Success Criteria:**
- Comprehensive admin tools for catalog management
- Bulk import capabilities for efficient catalog building
- Content moderation workflow

---

## Epic 10: Testing & Quality Assurance
**Priority: Medium** | **Estimated Points: 13**

### Tasks:
- [ ] **Update unit tests** - Modify existing tests for new release-focused functionality
- [ ] **Create integration tests** - Test complete workflows from search to platform links
- [ ] **Remove BitTorrent tests** - Delete all torrent-related test cases
- [ ] **Performance testing** - Ensure release search and browsing perform well
- [ ] **Security audit** - Review security implications of removed tracker functionality

**Success Criteria:**
- Comprehensive test coverage for new functionality
- No BitTorrent-related tests remain
- Performance meets or exceeds current standards

---

## Epic 11: Documentation & Deployment
**Priority: Low** | **Estimated Points: 8**

### Tasks:
- [ ] **Update user documentation** - Guides for using the music catalog
- [ ] **Update admin documentation** - Instructions for content management
- [ ] **Create migration guide** - Document migration process from tracker to catalog
- [ ] **Update deployment scripts** - Modify Docker and deployment for new structure
- [ ] **Create API documentation** - Document new release-focused API endpoints

**Success Criteria:**
- Complete documentation for new system
- Smooth deployment process
- Clear migration instructions

---

## Risk Mitigation Strategies

### High-Risk Dependencies
1. **Artist Pages** - Currently deeply integrated with torrent display
2. **Search System** - Heavily optimized for torrent metadata
3. **User Statistics** - Built around seeding/ratio metrics

### Mitigation Approaches
1. **Incremental Migration** - Maintain parallel systems during transition
2. **Feature Flags** - Toggle between old/new functionality during development
3. **Comprehensive Testing** - Test each component thoroughly before removing dependencies

---

## Success Metrics

### Technical Metrics
- [ ] Zero BitTorrent functionality remains
- [ ] All music metadata preserved and searchable
- [ ] Platform integration functional for 95%+ of releases
- [ ] Page load times ≤ current performance

### User Experience Metrics
- [ ] Users can discover music effectively
- [ ] Community features remain active
- [ ] Admin content management efficient
- [ ] Mobile experience seamless

---

## Timeline Estimate
**Total Points:** ~220 points
**Estimated Duration:** 12-16 weeks (assuming 15-20 points per week)

## Resource Requirements
- **Lead Developer:** Full-time for database and core logic
- **Frontend Developer:** 0.5 FTE for UI transformation  
- **QA Engineer:** 0.3 FTE for testing and validation
- **DevOps:** 0.2 FTE for deployment and infrastructure updates