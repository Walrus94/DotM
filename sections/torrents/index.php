<?php
if (!empty($_GET['searchstr'])) {
    header('Location: artist.php?artistname=' . urlencode($_GET['searchstr']));
    exit;
}
header('Location: index.php');
