<?php

namespace Gazelle\Manager;

class ReleaseLink extends \Gazelle\BaseManager {
    protected const ID_KEY    = 'rel_link_%d';
    protected const GROUP_KEY = 'rel_link_gid_%d';

    public function findById(int $id): ?\Gazelle\ReleaseLink {
        $key = sprintf(self::ID_KEY, $id);
        $id  = self::$cache->get_value($key);
        if ($id === false) {
            $id = self::$db->scalar(
                "SELECT ID FROM release_platform WHERE ID = ?",
                $id
            );
            if ($id) {
                self::$cache->cache_value($key, $id, 86400);
            }
        }
        return $id ? new \Gazelle\ReleaseLink($id) : null;
    }

    public function linkIdList(int $tgroupId): array {
        $key  = sprintf(self::GROUP_KEY, $tgroupId);
        $list = self::$cache->get_value($key);
        if ($list === false) {
            self::$db->prepared_query(
                "SELECT ID
                FROM release_platform
                WHERE ReleaseID = ?
                ORDER BY ID",
                $tgroupId
            );
            $list = self::$db->collect(0, false);
            self::$cache->cache_value($key, $list, 86400);
        }
        return $list;
    }
}
