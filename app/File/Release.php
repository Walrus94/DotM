<?php

namespace Gazelle\File;

class Release extends \Gazelle\File {
    /**
     * Path of a release file
     */
    public function path(mixed $id): string {
        $key = strrev(sprintf('%04d', $id));
        return sprintf('%s/%02d/%02d/%d.release',
            STORAGE_PATH_RELEASE, substr($key, 0, 2), substr($key, 2, 2), $id
        );
    }
}
