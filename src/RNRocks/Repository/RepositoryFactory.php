<?php

/*
 * This file is part of the Rhein Neckar Rocks Crawler project.
 *
 * (c) Rhein Neckar Rocks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RNRocks\Repository;

class RepositoryFactory
{
    /**
     * @var AtomFeedEventRepository
     */
    protected static $atomRepository;
    /**
     * @var RssFeedEventRepository
     */
    protected static $rssRepository;
    /**
     * @var MeetupEventRepository
     */
    protected static $meetupRepository;

    /**
     * Initializes the factory with the instances that should be returned.
     *
     * @param AtomFeedEventRepository $atomRepository
     * @param RssFeedEventRepository $rssRepository
     * @param MeetupEventRepository $meetupRepository
     */
    public static function initialize(AtomFeedEventRepository $atomRepository, RssFeedEventRepository $rssRepository, MeetupEventRepository $meetupRepository)
    {
        self::$atomRepository = $atomRepository;
        self::$rssRepository = $rssRepository;
        self::$meetupRepository = $meetupRepository;
    }

    /**
     * Returns the repository instance for the given $type.
     *
     * @param $type
     * @return EventRepository
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function create($type)
    {
        $type = strtolower($type);
        $repository = null;

        switch ($type) {
            case 'atom':
                $repository = self::$atomRepository;
                break;

            case 'rss':
                $repository = self::$rssRepository;
                break;

            case 'meetup':
                $repository = self::$meetupRepository;
                break;

            default:
                throw new \InvalidArgumentException(sprintf('No implementation registered for type "%s".', $type));
        }

        if (null !== $repository) {
            return $repository;
        }

        throw new \RuntimeException(sprintf('Repository for type "%s" not initialized.', $type));
    }
}
