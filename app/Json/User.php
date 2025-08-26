<?php

namespace Gazelle\Json;

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

        $stats        = $user->stats();
        $forumPosts   = $stats->forumPostTotal();
        $uploaded     = $this->valueOrNull($user->uploadedSize(),      'uploaded');
        $downloaded   = $this->valueOrNull($user->downloadedSize(),    'downloaded');
        $uploads      = $this->valueOrNull($stats->uploadTotal(),      'uploads+');
        $artistsAdded = $this->valueOrNull($stats->artistAddedTotal(), 'artistsadded');

        if (!$user->propertyVisibleMulti($viewer, ['requestsfilled_count', 'requestsfilled_bounty'])) {
            $requestsFilled = null;
            $totalBounty    = null;
            $requestsVoted  = null;
            $totalSpent     = null;
        } else {
            $requestsFilled = $stats->requestBountyTotal();
            $totalBounty    = $stats->requestBountySize();
            $requestsVoted  = $stats->requestVoteTotal();
            $totalSpent     = $stats->requestVoteSize();
        }

        $rank = new \Gazelle\UserRank(
            new \Gazelle\UserRank\Configuration(RANKING_WEIGHT),
            [
                'posts'      => $forumPosts,
                'artists'    => (int)$artistsAdded,
                'downloaded' => (int)$downloaded,
                'bounty'     => (int)$totalSpent,
                'requests'   => (int)$requestsFilled,
                'uploaded'   => (int)$uploaded,
                'uploads'    => (int)$uploads,
                'bonus'      => (new \Gazelle\User\Bonus($user))->pointsSpent(),
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
                'uploads'    => $this->valueOrNull($rank->rank('uploads'),    'uploads+'),
                'requests'   => $this->valueOrNull($rank->rank('requests'),   'requestsfilled_count'),
                'bounty'     => $this->valueOrNull($rank->rank('bounty'),     'requestsvoted_bounty'),
                'artists'    => $this->valueOrNull($rank->rank('artists'),    'artistsadded'),
                'posts'      => $rank->rank('posts'),
                'bonus'      => $rank->rank('bonus'),
                'overall'    => $user->propertyVisibleMulti($viewer, ['uploaded', 'downloaded', 'uploads+', 'requestsfilled_count', 'requestsvoted_bounty', 'artistsadded'])
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
                'requestsFilled'  => $requestsFilled,
                'bountyEarned'    => $totalBounty,
                'requestsVoted'   => $requestsVoted,
                'bountySpent'     => $totalSpent,
                'uploaded'        => $uploads,
                'artistsAdded'    => $artistsAdded,
                'artistComments'  => $this->valueOrNull($stats->commentTotal('artists'),  'torrentcomments++'),
                'requestComments' => $this->valueOrNull($stats->commentTotal('requests'), 'torrentcomments++'),
                'invited'         => $this->valueOrNull($stats->invitedTotal(),           'invitedcount'),
            ]
        ];
    }
}
