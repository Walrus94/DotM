<?php

namespace Gazelle\Task;

class DemoteUsersRatio extends \Gazelle\Task {
    public function run(): void {
        // Note: This task is disabled as it depends on torrent-related functionality
        // (users_leech_stats, ratio calculations, etc.) that has been removed for music catalog
        $this->info("DemoteUsersRatio task disabled - torrent functionality removed", 0);
    }

    private function demote(int $newClass, float $ratio, int $upload, array $demoteClasses, \Gazelle\Manager\User $userMan): void {
        $classString = $userMan->userclassName($newClass);
        $placeholders = placeholders($demoteClasses);
        $query = self::$db->prepared_query("
            SELECT ID
            FROM users_main um
            INNER JOIN users_leech_stats AS uls ON (uls.UserID = um.ID)
            LEFT JOIN
            (
                SELECT rv.UserID, sum(Bounty) AS Bounty
                FROM requests_votes rv
                INNER JOIN requests r ON (r.ID = rv.RequestID)
                WHERE r.UserID != r.FillerID
                GROUP BY rv.UserID
            ) b ON (b.UserID = um.ID)
            WHERE um.PermissionID IN ($placeholders)
                AND (
                    (uls.Downloaded > 0 AND uls.Uploaded / uls.Downloaded < ?)
                    OR (uls.Uploaded + ifnull(b.Bounty, 0)) < ?
                )
            ", ...array_merge($demoteClasses, [$ratio, $upload])
        );

        self::$db->prepared_query("
            UPDATE users_info AS ui
            INNER JOIN users_main AS um ON (um.ID = ui.UserID)
            INNER JOIN users_leech_stats AS uls ON (uls.UserID = um.ID)
            LEFT JOIN
            (
                SELECT rv.UserID, sum(Bounty) AS Bounty
                FROM requests_votes rv
                INNER JOIN requests r ON (r.ID = rv.RequestID)
                WHERE r.UserID != r.FillerID
                GROUP BY rv.UserID
            ) b ON (b.UserID = um.ID)
            SET
                um.PermissionID = ?,
                ui.AdminComment = concat(now(), ' - Class changed to ', ?, ' by System\n\n', ui.AdminComment)
            WHERE um.PermissionID IN ($placeholders)
                AND (
                    (uls.Downloaded > 0 AND uls.Uploaded / uls.Downloaded < ?)
                    OR (uls.Uploaded + ifnull(b.Bounty, 0)) < ?
                )
            ", ...array_merge([$newClass, $classString], $demoteClasses, [$ratio, $upload])
        );

        self::$db->set_query_id($query);
        $demotions = 0;
        while ([$userId] = self::$db->next_record()) {
            $user = $userMan->findById($userId);
            if (is_null($user)) {
                continue;
            }
            $demotions++;
            $this->debug("Demoting $userId to $classString for insufficient ratio", $userId);
            $user->flush();
            $user->inbox()->createSystem(
                "You have been demoted to $classString",
                "You now only meet the requirements for the \"$classString\" user class.\n\nTo read more about "
                    . SITE_NAME
                    . "'s user classes, read [url=wiki.php?action=article&name=userclasses]this wiki article[/url]."
            );
        }

        if ($demotions > 0) {
            $this->processed += $demotions;
            $this->info("Demoted $demotions users to $classString for insufficient ratio", $newClass);
        }
    }
}
