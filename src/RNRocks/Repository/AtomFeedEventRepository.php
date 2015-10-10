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

class AtomFeedEventRepository implements EventRepository
{
    /**
     * {@inheritdoc}
     */
    public function getEvents($url)
    {
        $events = [];

        $feed = file_get_contents($url);
        $feed = simplexml_load_string($feed);

        foreach ($feed->entry as $event) {
            $title = (string) $event->title;
            $date = date('Y-m-d', strtotime((string) $event->updated));
            $link = (string) $event->link['href'];

            $events[] = new Event($title, $date, $link);
        }

        return $events;
    }
}
