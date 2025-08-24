<?php
use Phinx\Migration\AbstractMigration;

class ReleaseArtistTag extends AbstractMigration {
    public function up(): void {
        // drop foreign keys that reference GroupID or release_id to allow column rename
        foreach (['torrents_artists', 'torrents_tags', 'torrents_tags_votes', 'release_artist', 'release_tag'] as $table) {
            foreach (['GroupID', 'release_id'] as $col) {
                $fks = $this->fetchAll(
                    "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE " .
                    "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $table . "' " .
                    "AND COLUMN_NAME = '" . $col . "' AND REFERENCED_TABLE_NAME IS NOT NULL"
                );
                foreach ($fks as $fk) {
                    $this->execute("ALTER TABLE {$table} DROP FOREIGN KEY {$fk['CONSTRAINT_NAME']}");
                }
            }
        }

        $tg = $this->fetchRow("SHOW TABLE STATUS WHERE Name = 'torrents_group'");
        $canFk = $tg && strtolower($tg['Engine']) === 'innodb';

        // torrents_artists -> release_artist (guard against previous partial runs)
        if ($this->hasTable('torrents_artists')) {
            $this->execute("RENAME TABLE torrents_artists TO release_artist");
            $this->execute("ALTER TABLE release_artist DROP PRIMARY KEY");
            if ($this->fetchRow("SHOW INDEX FROM release_artist WHERE Key_name = 'GroupID'")) {
                $this->execute("ALTER TABLE release_artist DROP INDEX GroupID");
            }
            $this->execute("ALTER TABLE release_artist CHANGE GroupID release_id int(10) NOT NULL");
            $this->execute("ALTER TABLE release_artist ADD INDEX release_id (release_id)");
            $this->execute("ALTER TABLE release_artist ADD PRIMARY KEY (release_id, ArtistID, Importance)");
            if ($canFk) {
                $this->execute("ALTER TABLE release_artist ADD FOREIGN KEY (release_id) REFERENCES torrents_group(ID)");
            }
        }

        // torrents_tags -> release_tag
        if ($this->hasTable('torrents_tags')) {
            $this->execute("RENAME TABLE torrents_tags TO release_tag");
            $this->execute("ALTER TABLE release_tag DROP PRIMARY KEY");
            if ($this->fetchRow("SHOW INDEX FROM release_tag WHERE Key_name = 'GroupID'")) {
                $this->execute("ALTER TABLE release_tag DROP INDEX GroupID");
            }
            $this->execute("ALTER TABLE release_tag CHANGE GroupID release_id int(10) NOT NULL DEFAULT '0'");
            $this->execute("ALTER TABLE release_tag ADD INDEX release_id (release_id)");
            $this->execute("ALTER TABLE release_tag ADD PRIMARY KEY (TagID, release_id)");
            if ($canFk) {
                $this->execute("ALTER TABLE release_tag ADD FOREIGN KEY (release_id) REFERENCES torrents_group(ID)");
            }
        }

        // update votes table
        if ($this->table('torrents_tags_votes')->hasColumn('GroupID')) {
            if ($this->fetchRow("SHOW INDEX FROM torrents_tags_votes WHERE Key_name = 'GroupID'")) {
                $this->execute("ALTER TABLE torrents_tags_votes DROP INDEX GroupID");
            }
            $this->execute("ALTER TABLE torrents_tags_votes CHANGE GroupID release_id int(10) NOT NULL");
            $this->execute("ALTER TABLE torrents_tags_votes ADD INDEX release_id (release_id)");
            if ($canFk) {
                $this->execute("ALTER TABLE torrents_tags_votes ADD FOREIGN KEY (release_id) REFERENCES torrents_group(ID)");
            }
        }
    }

    public function down(): void {
        $tg = $this->fetchRow("SHOW TABLE STATUS WHERE Name = 'torrents_group'");
        $canFk = $tg && strtolower($tg['Engine']) === 'innodb';

        // drop foreign keys on release_id or GroupID before reverting
        foreach (['release_artist', 'release_tag', 'torrents_tags_votes'] as $table) {
            foreach (['release_id', 'GroupID'] as $col) {
                $fks = $this->fetchAll(
                    "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE " .
                    "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $table . "' " .
                    "AND COLUMN_NAME = '" . $col . "' AND REFERENCED_TABLE_NAME IS NOT NULL"
                );
                foreach ($fks as $fk) {
                    $this->execute("ALTER TABLE {$table} DROP FOREIGN KEY {$fk['CONSTRAINT_NAME']}");
                }
            }
        }

        // revert votes table
        if ($this->fetchRow("SHOW INDEX FROM torrents_tags_votes WHERE Key_name = 'release_id'")) {
            $this->execute("ALTER TABLE torrents_tags_votes DROP INDEX release_id");
        }
        $this->execute("ALTER TABLE torrents_tags_votes CHANGE release_id GroupID int(10) NOT NULL");
        $this->execute("ALTER TABLE torrents_tags_votes ADD INDEX GroupID (GroupID)");
        if ($canFk) {
            $this->execute("ALTER TABLE torrents_tags_votes ADD FOREIGN KEY (GroupID) REFERENCES torrents_group(ID)");
        }

        // release_tag -> torrents_tags
        $this->execute("ALTER TABLE release_tag DROP PRIMARY KEY");
        if ($this->fetchRow("SHOW INDEX FROM release_tag WHERE Key_name = 'release_id'")) {
            $this->execute("ALTER TABLE release_tag DROP INDEX release_id");
        }
        $this->execute("ALTER TABLE release_tag CHANGE release_id GroupID int(10) NOT NULL DEFAULT '0'");
        $this->execute("ALTER TABLE release_tag ADD INDEX GroupID (GroupID)");
        $this->execute("ALTER TABLE release_tag ADD PRIMARY KEY (TagID, GroupID)");
        $this->execute("RENAME TABLE release_tag TO torrents_tags");
        if ($canFk) {
            $this->execute("ALTER TABLE torrents_tags ADD FOREIGN KEY (GroupID) REFERENCES torrents_group(ID)");
        }

        // release_artist -> torrents_artists
        $this->execute("ALTER TABLE release_artist DROP PRIMARY KEY");
        if ($this->fetchRow("SHOW INDEX FROM release_artist WHERE Key_name = 'release_id'")) {
            $this->execute("ALTER TABLE release_artist DROP INDEX release_id");
        }
        $this->execute("ALTER TABLE release_artist CHANGE release_id GroupID int(10) NOT NULL");
        $this->execute("ALTER TABLE release_artist ADD INDEX GroupID (GroupID)");
        $this->execute("ALTER TABLE release_artist ADD PRIMARY KEY (GroupID, ArtistID, Importance)");
        $this->execute("RENAME TABLE release_artist TO torrents_artists");
        if ($canFk) {
            $this->execute("ALTER TABLE torrents_artists ADD FOREIGN KEY (GroupID) REFERENCES torrents_group(ID)");
        }
    }
}
