<?php
/** @phpstan-var \Gazelle\User $Viewer */

if (!$Viewer->permitted('site_archive_ajax')) {
    json_die('failure', 'insufficient permissions to view page');
}

$where = ["t.HasLog='1'", "t.HasLogDB='0'"];

$where = implode(' AND ', $where);
$db = Gazelle\DB::DB();
$db->prepared_query("SELECT t.ID FROM torrents t WHERE {$where}");

json_print('success', ['IDs' => $db->collect('ID', false)]);
