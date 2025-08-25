<?php

namespace Gazelle;

class Feed extends Base {
    public function header(): string {
        header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma:');
        header('Expires: ' . date('D, d M Y H:i:s', time() + (2 * 60 * 60)) . ' GMT');
        header('Last-Modified: ' . date('D, d M Y H:i:s') . ' GMT');
        header("Content-type: application/xml; charset=UTF-8");

        return self::$twig->render('feed/header.twig');
    }

    public function footer(): string {
        return self::$twig->render('feed/footer.twig');
    }

    public function channel(string $title, string $description): string {
        return self::$twig->render('feed/channel.twig', [
            'date'        => date('r'),
            'description' => $description,
            'title'       => $title,
        ]);
    }

    public function item(string $title, string $description, string $page, string $creator, string $date, string $comments = '', string $category = ''): string {
        return self::$twig->render('feed/item.twig', [
            'category'    => $category,
            'comments'    => $comments,
            'creator'     => $creator,
            'date'        => date('r', strtotime($date)),
            'description' => $description,
            'page'        => SITE_URL . "/$page",
            'title'       => $title,
        ]);
    }

    public function retrieve(User $user, string $key): string {
        $list = self::$cache->get_value($key);
        if ($list === false) {
            $list = [];
        }
        $announceKey = $user->announceKey();
        return implode('', array_map(fn ($item) => str_replace('[[PASSKEY]]', $announceKey, $item), $list));
    }

    public function populate(string $key, string $item): int {
        $list = self::$cache->get_value($key);
        if ($list === false) {
            $list = [];
        }
        array_unshift($list, $item);
        $list = array_slice($list, 0, 50);
        self::$cache->cache_value($key, $list, 0);
        return count($list);
    }

    ### EMITTER METHODS ###

    public function blocked(): string {
        return $this->wrap($this->channel('Blocked', 'RSS feed.'));
    }

    public function blog(
        Manager\Blog $blogMan,
        Manager\ForumThread $threadMan,
    ): string {
        return $this->wrap(
            $this->channel(
                'Blog',
                'RSS feed for site blog.'
            )
            . implode('',
                array_map(
                    fn ($b) => $this->item(
                        $b->title(),
                        \Text::strip_bbcode($b->body()),
                        $threadMan->findById((int)$b->threadId())?->url() ?? $b->url(),
                        SITE_NAME . ' Staff',
                        $b->created(),
                    ),
                    $blogMan->headlines()
                )
            )
        );
    }

    public function bookmark(User $user, string $feedName): string {
        return $this->wrap(
            $this->channel(
                'Bookmarked edition notifications',
                'RSS feed for bookmarked editions'
            )
            . $this->retrieve($user, $feedName)
        );
    }

    public function byFeedName(User $user, string $feedName): string {
        return $this->wrap(
            $this->channel(
                match ($feedName) {
                    'releases_all'        => 'Everything',
                    'releases_apps'       => 'Applications',
                    'releases_abooks'     => 'Audiobooks',
                    'releases_comedy'     => 'Comedy',
                    'releases_comics'     => 'Comics',
                    'releases_ebooks'     => 'E-Books',
                    'releases_evids'      => 'E-Learning Videos',
                    'releases_flac'       => 'FLACs',
                    'releases_lossless'   => 'Lossless',
                    'releases_music'      => 'Music',
                    'releases_mp3'        => 'MP3s',
                    'releases_vinyl'      => 'Vinyl',
                    'releases_lossless24' => '24bit Lossless',
                },
                match ($feedName) {
                    'releases_all'        => 'RSS feed for new release uploads',
                    'releases_apps'       => 'RSS feed for new application releases',
                    'releases_comedy'     => 'RSS feed for new comedy releases',
                    'releases_comics'     => 'RSS feed for new comics releases',
                    'releases_abooks'     => 'RSS feed for new audiobook release uploads',
                    'releases_ebooks'     => 'RSS feed for new e-book releases',
                    'releases_evids'      => 'RSS feed for new e-learning video releases',
                    'releases_flac'       => 'RSS feed for new FLAC releases',
                    'releases_lossless'   => 'RSS feed for new lossless releases',
                    'releases_mp3'        => 'RSS feed for new MP3 releases',
                    'releases_music'      => 'RSS feed for new music releases',
                    'releases_vinyl'      => 'RSS feed for new vinyl-sourced releases',
                    'releases_lossless24' => 'RSS feed for new 24bit lossless releases',
                }
            ) . $this->retrieve($user, $feedName)
        );
    }

    public function changelog(Manager\Changelog $manager): string {
        return $this->wrap(
            $this->channel(
                SITE_NAME . ' Changelog',
                'RSS feed for ' . SITE_NAME . '\'s changelog.'
            )
            . implode('',
                array_map(
                    fn ($c) => $this->item(
                        "{$c['created']} by {$c['author']}",
                        $c['message'],
                        'tools.php?action=change_log',
                        SITE_NAME . ' Staff',
                        $c['created'],
                    ),
                    $manager->headlines()
                )
            )
        );
    }

    public function news(Manager\News $manager): string {
        return $this->wrap(
            $this->channel(
                SITE_NAME . ' News',
                'RSS feed for site news.'
            )
            . implode('',
                array_map(
                    fn ($n) => $this->item(
                        $n['title'],
                        \Text::strip_bbcode($n['body']),
                        "index.php#news{$n['id']}",
                        SITE_NAME . ' Staff',
                        $n['created'],
                    ),
                    $manager->list(5, 0)
                )
            )
        );
    }

    public function personal(User $user, string $feedName, ?string $filterName): string {
        return $this->wrap(
            $this->channel(
                'Personal notifications',
                'RSS feed for your ' . (is_null($filterName) ? 'notifications' : ('"' . display_str($filterName) . '" filter')),
            )
            . $this->retrieve($user, $feedName)
        );
    }

    public function wrap(string $body): string {
        return $this->header() . $body . $this->footer();
    }
}
