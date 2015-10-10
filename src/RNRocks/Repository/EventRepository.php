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

use RNRocks\Event;

interface EventRepository
{
    /**
     * Returns a list of events for the given $url.
     *
     * @param $url
     * @return Event[]
     */
    public function getEvents($url);
}
