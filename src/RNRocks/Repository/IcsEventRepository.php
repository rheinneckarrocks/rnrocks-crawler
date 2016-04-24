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

use om\IcalParser;
use RNRocks\Event;

class IcsEventRepository implements EventRepository
{
    /**
     * {@inheritdoc}
     */
    public function getEvents($url)
    {
        $events = [];

        $cal = new IcalParser();
        $cal->parseFile($url);

        foreach ($cal->getSortedEvents() as $event) {
            $venue = null;
            if (isset($event['LOCATION'])) {
                $venue = $event['LOCATION'];
            }

            $events[] = new Event($event['SUMMARY'], $event['DTSTART']->format('Y-m-d'), $event['URL'], $venue);
        }

        return $events;
    }
}
