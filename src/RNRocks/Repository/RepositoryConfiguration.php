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

use bitExpert\Disco\Annotations\Bean;
use \bitExpert\Disco\Annotations\Configuration;
use bitExpert\Disco\Annotations\Parameter;
use bitExpert\Disco\Annotations\Parameters;
use \RNRocks\Repository\AtomFeedEventRepository;
use RNRocks\Repository\IcsEventRepository;
use \RNRocks\Repository\RssFeedEventRepository;
use \RNRocks\Repository\MeetupEventRepository;

/**
 * @Configuration
 */
trait RepositoryConfiguration
{
    /**
     * @Bean
     * @return \RNRocks\Repository\AtomFeedEventRepository
     */
    public function atom()
    {
        $bean = new AtomFeedEventRepository();
        return $bean;
    }

    /**
     * @Bean
     * @return \RNRocks\Repository\RssFeedEventRepository
     */
    public function rss()
    {
        $bean = new RssFeedEventRepository();
        return $bean;
    }

    /**
     * @Bean
     * @Parameters({
     *  @Parameter({"name" = "MEETUP_API_KEY"})
     * })
     * @return \RNRocks\Repository\MeetupEventRepository
     */
    public function meetup($key = '')
    {
        $bean = new MeetupEventRepository($key);
        return $bean;
    }

    /**
     * @Bean
     * @return \RNRocks\Repository\IcsEventRepository
     */
    public function ics()
    {
        $bean = new IcsEventRepository();
        return $bean;
    }
}
