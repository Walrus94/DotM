<?php
use Phinx\Migration\AbstractMigration;

class ReleaseArtistTag extends AbstractMigration {
    public function up(): void {
        // drop foreign keys on GroupID to allow column rename
        foreach (['torrents_artists', 'torrents_tags', 'torrents_tags_votes'] as $table) {
            $fk = $this->fetchRow(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE " .
                "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $table . "' " .
                "AND COLUMN_NAME = 'GroupID' AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            if ($fk && isset($fk['CONSTRAINT_NAME'])) {
                $this->execute("ALTER TABLE {$table} DROP FOREIGN KEY {$fk['CONSTRAINT_NAME']}");
            }
        }

        // torrents_artists -> release_artist
        $this->execute("RENAME TABLE torrents_artists TO release_artist");
        $this->execute("ALTER TABLE release_artist DROP PRIMARY KEY");
        $this->execute("ALTER TABLE release_artist DROP INDEX GroupID");
        $this->execute("ALTER TABLE release_artist CHANGE GroupID release_id int(10) NOT NULL");
        $this->execute("ALTER TABLE release_artist ADD INDEX release_id (release_id)");
        $this->execute("ALTER TABLE release_artist ADD PRIMARY KEY (release_id, ArtistID, Importance)");
        $this->execute("ALTER TABLE release_artist ADD FOREIGN KEY (release_id) REFERENCES torrents_group(ID)");

        // torrents_tags -> release_tag
        $this->execute("RENAME TABLE torrents_tags TO release_tag");
        $this->execute("ALTER TABLE release_tag DROP PRIMARY KEY");
        $this->execute("ALTER TABLE release_tag DROP INDEX GroupID");
        $this->execute("ALTER TABLE release_tag CHANGE GroupID release_id int(10) NOT NULL DEFAULT '0'");
        $this->execute("ALTER TABLE release_tag ADD INDEX release_id (release_id)");
        $this->execute("ALTER TABLE release_tag ADD PRIMARY KEY (TagID, release_id)");
        $this->execute("ALTER TABLE release_tag ADD FOREIGN KEY (release_id) REFERENCES torrents_group(ID)");

        // update votes table
        $this->execute("ALTER TABLE torrents_tags_votes DROP INDEX GroupID");
        $this->execute("ALTER TABLE torrents_tags_votes CHANGE GroupID release_id int(10) NOT NULL");
        $this->execute("ALTER TABLE torrents_tags_votes ADD INDEX release_id (release_id)");
        $this->execute("ALTER TABLE torrents_tags_votes ADD FOREIGN KEY (release_id) REFERENCES torrents_group(ID)");
    }

    public function down(): void {
        // drop foreign keys on release_id before reverting
        foreach (['release_artist', 'release_tag', 'torrents_tags_votes'] as $table) {
            $fk = $this->fetchRow(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE " .
                "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $table . "' " .
                "AND COLUMN_NAME = 'release_id' AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            if ($fk && isset($fk['CONSTRAINT_NAME'])) {
                $this->execute("ALTER TABLE {$table} DROP FOREIGN KEY {$fk['CONSTRAINT_NAME']}");
            }
        }

        // revert votes table
        $this->execute("ALTER TABLE torrents_tags_votes DROP INDEX release_id");
        $this->execute("ALTER TABLE torrents_tags_votes CHANGE release_id GroupID int(10) NOT NULL");
        $this->execute("ALTER TABLE torrents_tags_votes ADD INDEX GroupID (GroupID)");
        $this->execute("ALTER TABLE torrents_tags_votes ADD FOREIGN KEY (GroupID) REFERENCES torrents_group(ID)");

        // release_tag -> torrents_tags
        $this->execute("ALTER TABLE release_tag DROP PRIMARY KEY");
        $this->execute("ALTER TABLE release_tag DROP INDEX release_id");
        $this->execute("ALTER TABLE release_tag CHANGE release_id GroupID int(10) NOT NULL DEFAULT '0'");
        $this->execute("ALTER TABLE release_tag ADD INDEX GroupID (GroupID)");
        $this->execute("ALTER TABLE release_tag ADD PRIMARY KEY (TagID, GroupID)");
        $this->execute("RENAME TABLE release_tag TO torrents_tags");
        $this->execute("ALTER TABLE torrents_tags ADD FOREIGN KEY (GroupID) REFERENCES torrents_group(ID)");

        // release_artist -> torrents_artists
        $this->execute("ALTER TABLE release_artist DROP PRIMARY KEY");
        $this->execute("ALTER TABLE release_artist DROP INDEX release_id");
        $this->execute("ALTER TABLE release_artist CHANGE release_id GroupID int(10) NOT NULL");
        $this->execute("ALTER TABLE release_artist ADD INDEX GroupID (GroupID)");
        $this->execute("ALTER TABLE release_artist ADD PRIMARY KEY (GroupID, ArtistID, Importance)");
        $this->execute("RENAME TABLE release_artist TO torrents_artists");
        $this->execute("ALTER TABLE torrents_artists ADD FOREIGN KEY (GroupID) REFERENCES torrents_group(ID)");
    }
}
