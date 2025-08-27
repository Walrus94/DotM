<?php
/** @phpstan-var \Gazelle\User $Viewer */
/** @phpstan-var \Twig\Environment $Twig */

if (!empty($_POST['action'])) {
    match ($_POST['action']) {
        'add_similar'     => include_once 'add_similar.php',
        'add_alias'       => include_once 'add_alias.php',
        'change_artistid' => include_once 'change_artistid.php',
        'download'        => include_once 'download.php',
        'rename'          => include_once 'rename.php',
        'edit'            => include_once 'edit_handle.php',
        'takeeditrequest' => include_once 'edit_request_handle.php',
        default           => error('Missing artist POST action'),
    };
} elseif (!empty($_GET['action'])) {
    match ($_GET['action']) {
        'autocomplete'    => include_once 'autocomplete.php',
        'change_artistid' => include_once 'change_artistid.php',
        'delete'          => include_once 'delete.php',
        'delete_alias'    => include_once 'delete_alias.php',
        'delete_similar'  => include_once 'delete_similar.php',
        'edit'            => include_once 'edit.php',
        'editrequest'     => include_once 'edit_request.php',
        'history'         => include_once 'history.php',
        'notify'          => include_once 'notify.php',
        'notifyremove'    => include_once 'notify_remove.php',
        'revert'          => include_once 'edit_handle.php',
        'vote_similar'    => include_once 'vote_similar.php',
        default           => error('Missing artist action'),
    };
} else {
    if (!empty($_GET['id'])) {
        include_once 'artist.php';
    } elseif (empty($_GET['artistname'])) {
        header('Location: torrents.php');
    } else {
        $db = Gazelle\DB::DB();
        $NameSearch = trim($_GET['artistname']);
        $db->prepared_query("
            SELECT aa.ArtistID, aa.Name
            FROM artists_alias aa
            INNER JOIN release_artist ra ON (ra.AliasID = aa.AliasID)
            WHERE aa.Name LIKE concat(?, '%')
            GROUP BY aa.ArtistID, aa.Name
            ORDER BY aa.Name
            ", $NameSearch
        );
        $results = $db->to_array(false, MYSQLI_NUM, false);
        if (!$results) {
            echo $Twig->render('artist/search.twig', [
                'query'   => $NameSearch,
                'results' => [],
            ]);
            return;
        }
        foreach ($results as [$ID, $Name]) {
            if (!strcasecmp($Name, $NameSearch)) {
                header("Location: artist.php?id=$ID");
                exit;
            }
        }
        echo $Twig->render('artist/search.twig', [
            'query'   => $NameSearch,
            'results' => array_map(fn($r) => ['id' => $r[0], 'name' => $r[1]], $results),
        ]);
    }
}
