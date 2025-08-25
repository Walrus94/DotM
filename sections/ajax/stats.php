<?php

echo (new Gazelle\Json\Stats\General(
    new Gazelle\Stats\Request(),
    new Gazelle\Stats\Release(),
    new Gazelle\Stats\Users(),
))
    ->setVersion(2)
    ->response();
