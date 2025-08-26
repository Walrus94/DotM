<?php
/** @phpstan-var \Gazelle\User $Viewer */

$groupId = (int)($_GET['id'] ?? 0);
if (!$groupId) {
    json_error('bad parameters');
}

$rgMan  = new Gazelle\Manager\RGroup();
$tgroup = $rgMan->findById($groupId);
if (is_null($tgroup)) {
    json_error('bad parameters');
}

echo (new Gazelle\Json\RGroup(
        $tgroup,
        $Viewer,
        new \Gazelle\Manager\ReleaseLink()
    ))->setVersion(2)
    ->response();
