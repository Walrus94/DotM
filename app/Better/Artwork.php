<?php

namespace Gazelle\Better;

class Artwork extends AbstractBetter {
    public function mode(): string {
        return 'group';
    }

    public function heading(): string {
        return 'Releases without image artwork/cover art';
    }

    public function configure(): void {
        $this->field = 'tg.ID';
        $this->baseQuery = "
            FROM torrents_group tg
            LEFT JOIN wiki_torrents wt USING (RevisionID)";

        $this->where[] = "tg.CategoryID = 1 AND coalesce(wt.Image, tg.WikiImage) = ''";
        $this->orderBy = "ORDER BY tg.Name";

        if ($this->filter === 'snatched') {
            $this->where[] = "EXISTS (
                SELECT 1
                FROM xbt_snatched xs
                INNER JOIN torrents t ON (t.ID = xs.fid AND xs.uid = ?)
                WHERE t.GroupID = tg.ID)";
            $this->args[]     = $this->user->id();
        } elseif ($this->filter === 'uploaded') {
            $this->where[] = "EXISTS (
                SELECT 1
                FROM torrents t
                WHERE t.GroupID = tg.ID
                    AND t.UserID = ?)";
            $this->args[] = $this->user->id();
        }
    }
}
