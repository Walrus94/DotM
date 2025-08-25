<?php

echo (new Gazelle\Json\Stats\Release(new Gazelle\Stats\Release()))
    ->setVersion(2)
    ->response();
