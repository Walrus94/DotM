<?php
/** @phpstan-var \Gazelle\User $Viewer */
/** @phpstan-var \Twig\Environment $Twig */

$releaseId = (int)($_GET['id'] ?? 0);
if (!$releaseId) {
    error(404);
}

$releaseMan = new \Gazelle\Manager\Release();
$release = $releaseMan->findById($releaseId);

if (!$release) {
    error(404);
}

// Get artist information
$artistRole = $release->artistRole();

// Get platform links
$platforms = $release->platforms();

// Get comments (adapt existing comment system)
$commentMan = new \Gazelle\Manager\Comment();
$comments = $commentMan->findByPage('releases', $releaseId);

echo $Twig->render('release/view.twig', [
    'release'      => $release,
    'artist_role'  => $artistRole,
    'platforms'    => $platforms,
    'comments'     => $comments,
    'viewer'       => $Viewer,
]);