<?php

namespace GazelleUnitTest;

use PHPUnit\Framework\TestCase;

class ArtistUsageTest extends TestCase {
    public function testUsageTotalUsesReleaseIds(): void {
        $artist = new class extends \Gazelle\Artist {
            public function __construct() {}
            public function requestIdUsage(): array { return [1, 2]; }
            public function releaseIdUsage(): array { return [3, 4]; }
            public function tgroupIdUsage(): array { return [5, 6, 7]; }
        };
        $this->assertSame(4, $artist->usageTotal());
    }
}
