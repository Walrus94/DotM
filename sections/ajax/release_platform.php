<?php
/** @phpstan-var \Gazelle\User $Viewer */

$mode = $_POST['mode'] ?? $_GET['mode'] ?? 'list';
$releaseId = (int)($_POST['release_id'] ?? $_GET['release_id'] ?? 0);
$manager = new Gazelle\Manager\ReleasePlatform();

switch ($mode) {
    case 'create':
        authorize();
        $platform = $_POST['platform'] ?? '';
        $url = $_POST['url'] ?? '';
        if (!$releaseId || !$platform || !$url) {
            json_error('missing parameters');
        }
        $rp = $manager->create($releaseId, $platform, $url);
        json_print('success', ['id' => $rp->id()]);
        break;
    case 'update':
        authorize();
        $id = (int)($_POST['id'] ?? 0);
        $platform = $_POST['platform'] ?? '';
        $url = $_POST['url'] ?? '';
        if (!$id || !$platform || !$url) {
            json_error('missing parameters');
        }
        $manager->update($id, $platform, $url);
        json_print('success', ['status' => 'ok']);
        break;
    case 'delete':
        authorize();
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            json_error('missing parameters');
        }
        $manager->remove($id);
        json_print('success', ['status' => 'ok']);
        break;
    case 'list':
    default:
        if (!$releaseId) {
            json_error('missing parameters');
        }
        json_print('success', ['platforms' => $manager->listByRelease($releaseId)]);
        break;
}
