<?php

namespace Gazelle\Better;

class SingleSeeded extends AbstractBetter {
    public function mode(): string {
        return 'torrent';
    }

    public function heading(): string {
        return 'FLAC releases with a single seeder';
    }

    public function setUploader(\Gazelle\User $user): static {
        $this->where[] = "t.UserID = ?";
        $this->args[] = $user->id();
        return $this;
    }

    public function configure(): void {
        $this->field = 't.ID';
        $this->baseQuery = "
            FROM torrents t
            INNER JOIN torrents_group tg ON (tg.ID = t.GroupID)
        ";

        $this->where[] = "t.Format = 'FLAC'";
        $this->orderBy = "ORDER BY t.LogScore DESC, rand()";
    }
}
