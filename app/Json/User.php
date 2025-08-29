<?php

namespace Gazelle\Json;

use Gazelle\User\Vote;

class User extends \Gazelle\Json {
    public function __construct(
        protected \Gazelle\User $user,
        protected \Gazelle\User $viewer,
    ) {}

    protected function valueOrNull(int $value, string $property): ?int {
        return $this->user->propertyVisible($this->viewer, $property) ? $value : null;
    }

    public function payload(): array {
        $user   = $this->user;
        $viewer = $this->viewer;

        $stats           = $user->stats();
        $forumPosts      = $stats->forumPostTotal();
        $releaseVotes    = (new Vote($user))->userTotal(Vote::UPVOTE | Vote::DOWNVOTE);
        $uploaded        = $this->valueOrNull($user->uploadedSize(),            'uploaded');
        $downloaded      = $this->valueOrNull($user->downloadedSize(),          'downloaded');
        $uploads         = 0; // Note: Torrent uploads disabled for music catalog
        $artistsAdded    = $this->valueOrNull($stats->artistAddedTotal(),       'artistsadded');
        $torrentComments = $this->valueOrNull($stats->commentTotal('torrents'), 'torrentcomments++');
        $collageContribs = $this->valueOrNull($stats->collageContrib(),         'collagecontribs+');

        if (!$user->propertyVisibleMulti($viewer, ['requestsfilled_count', 'requestsfilled_bounty'])) {
            $requestsFilled = null;
            $totalBounty    = null;
            $requestsVoted  = null;
            $totalSpent     = null;
        } else {
            // Note: Request system disabled for music catalog - return safe defaults
            $requestsFilled = 0;
            $totalBounty    = 0;
            $requestsVoted  = 0;
            $totalSpent     = 0;
        }

        $rank = new \Gazelle\UserRank(
            new \Gazelle\UserRank\Configuration(RANKING_WEIGHT),
            [
                'posts'      => $forumPosts,
                'votes'      => $releaseVotes,
                'artists'    => (int)$artistsAdded,
                'downloaded' => (int)$downloaded,
                'bounty'     => 0, // Note: Request bounty system disabled for music catalog
                'collage'    => (int)$collageContribs,
                // 'comment-t'  => (int)$torrentComments, // DISABLED for music catalog
                'requests'   => 0, // Note: Request system disabled for music catalog
                'uploaded'   => (int)$uploaded,
                'uploads'    => 0, // Note: Torrent uploads disabled for music catalog
                'bonus'      => 0, // Note: Bonus system disabled for music catalog
            ]
        );

        return [
            'username'    => $user->username(),
            'avatar'      => $user->avatar(),
            'isFriend'    => (new \Gazelle\User\Friend($user))->isFriend($viewer),
            'profileText' => \Text::full_format($user->profileInfo()),
            'stats' => [
                'joinedDate'    => $user->created(),
                'lastAccess'    => match (true) {
                    $viewer->id() == $user->id()                => $user->lastAccessRealtime(),
                    $viewer->isStaff()                          => $user->lastAccessRealtime(),
                    $user->propertyVisible($viewer, 'lastseen') => $user->lastAccess(),
                    default                                     => null,
                },
                'uploaded'      => $uploaded,
                'downloaded'    => $downloaded,
                'requiredRatio' => $user->propertyVisible($viewer, 'requiredratio') ? $user->requiredRatio() : null,
                'ratio'         => match (true) {
                    is_null($uploaded) || is_null($downloaded)
                                 => null,
                    !$downloaded => 0.0,
                    default      => (float)ratio($uploaded, $downloaded, 5),
                },
            ],
            'ranks' => [
                'uploaded'   => $this->valueOrNull($rank->rank('uploaded'),   'uploaded'),
                'downloaded' => $this->valueOrNull($rank->rank('downloaded'), 'downloaded'),
                // 'uploads'    => $this->valueOrNull($rank->rank('uploads'),    'uploads+'), // DISABLED for music catalog
                'requests'   => $this->valueOrNull($rank->rank('requests'),   'requestsfilled_count'),
                'bounty'     => $this->valueOrNull($rank->rank('bounty'),     'requestsvoted_bounty'),
                'artists'    => $this->valueOrNull($rank->rank('artists'),    'artistsadded'),
                'collage'    => $this->valueOrNull($rank->rank('collage'),    'collagecontribs+'),
                'posts'      => $rank->rank('posts'),
                'votes'      => $rank->rank('votes'),
                'bonus'      => $rank->rank('bonus'),
                'overall'    => $user->propertyVisibleMulti($viewer, ['uploaded', 'downloaded', 'artistsadded', 'collagecontribs+'])
                    ? $rank->score() * $user->rankFactor() : null,
            ],
            'personal' => [
                'class'        => $user->userclassName(),
                'paranoia'     => $user->paranoiaLevel(),
                'paranoiaText' => $user->paranoiaLabel(),
                'donor'        => (new \Gazelle\User\Donor($user))->isDonor(),
                'warned'       => $user->isWarned(),
                'enabled'      => $user->isEnabled(),
                'passkey'      => ($user->id() === $viewer->id() || $viewer->isStaff()) ? $user->announceKey() : null,
            ],
            'community' => [
                'posts'           => $forumPosts,
                'torrentComments' => $torrentComments,
                'collagesContrib' => $collageContribs,
                'requestsFilled'  => $requestsFilled,
                'bountyEarned'    => $totalBounty,
                'requestsVoted'   => $requestsVoted,
                'bountySpent'     => $totalSpent,
                'releaseVotes'    => $releaseVotes,
                'uploaded'        => $uploads,
                'artistsAdded'    => $artistsAdded,
                'artistComments'  => $this->valueOrNull($stats->commentTotal('artists'),  'torrentcomments++'),
                'collageComments' => $this->valueOrNull($stats->commentTotal('collages'), 'torrentcomments++'),
                'requestComments' => $this->valueOrNull($stats->commentTotal('requests'), 'torrentcomments++'),
                'collagesStarted' => $this->valueOrNull($user->collagesCreated(),         'collages+'),
                'perfectFlacs'    => 0, // Note: Torrent system disabled for music catalog
                'groups'          => 0, // Note: Torrent system disabled for music catalog
                'seeding'         => 0, // Note: Torrent system disabled for music catalog
                'leeching'        => 0, // Note: Torrent system disabled for music catalog
                'snatched'        => 0, // Note: Torrent system disabled for music catalog
                'invited'         => $this->valueOrNull($stats->invitedTotal(),           'invitedcount'),
            ]
        ];
    }
}
