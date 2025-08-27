<?php
/** @phpstan-var \Gazelle\User $Viewer */
/** @phpstan-var \Twig\Environment $Twig */

use Gazelle\Manager\Release;

$search = trim($_GET['searchstr'] ?? '');
$results = [];

$releaseMan = new Release();

if ($search !== '') {
    $results = $releaseMan->search($search);
} else {
    // Show recent releases if no search
    $results = $releaseMan->recentReleases(25);
    // Convert to same format as search results
    foreach ($results as &$result) {
        $result['platforms'] = [];
        if ($result['ID']) {
            $platformMan = new \Gazelle\Manager\ReleasePlatform();
            $release = $releaseMan->findById($result['ID']);
            if ($release) {
                $result['platforms'] = $platformMan->findByRelease($release);
            }
        }
    }
}

echo $Twig->render('release/search.twig', [
    'query'        => $search,
    'results'      => $results,
    'total_count'  => $releaseMan->totalCount(),
    'viewer'       => $Viewer,
]);