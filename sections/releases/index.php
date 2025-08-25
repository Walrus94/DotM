<?php

use Gazelle\DB; 

$search = trim($_GET['searchstr'] ?? '');
$results = [];
if ($search !== '') {
    $db = DB::DB();
    $db->prepared_query(
        "SELECT tg.ID,
                tg.Name,
                tg.Year,
                group_concat(DISTINCT aa.Name ORDER BY aa.Name SEPARATOR ', ') AS ArtistName
           FROM torrents_group tg
           LEFT JOIN release_artist ra ON (ra.release_id = tg.ID)
           LEFT JOIN artists_alias aa ON (aa.AliasID = ra.AliasID)
          WHERE tg.Name LIKE concat('%', ?, '%')
          GROUP BY tg.ID, tg.Name, tg.Year
          ORDER BY tg.Year DESC, tg.Name
          LIMIT 50",
        $search
    );
    $results = $db->to_array('ID', MYSQLI_ASSOC);
    $ids = array_keys($results);
    if ($ids) {
        $db->prepared_query(
            'SELECT ReleaseID, Platform, Url
               FROM release_platform
              WHERE ReleaseID IN (' . placeholders($ids) . ')
              ORDER BY Platform',
            ...$ids
        );
        $platform = [];
        while ([$rid, $p, $u] = $db->next_record(MYSQLI_NUM)) {
            $platform[$rid][] = ['Platform' => $p, 'Url' => $u];
        }
        foreach ($results as $id => &$row) {
            $row['platforms'] = $platform[$id] ?? [];
        }
    }
}

echo $Twig->render('release/search.twig', [
    'query'   => $search,
    'results' => $results,
]);
