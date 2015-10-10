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

class RssFeedEventRepository implements EventRepository
{
    /**
     * {@inheritdoc}
     */
    public function getEvents($url)
    {
        $events = [];

        $feed = file_get_contents($url);
        $feed = simplexml_load_string($feed);

        foreach ($feed->channel->item as $event) {
            $title = trim((string) $event->title);
            $date = $this->extractDate($title);
            $link = (string) $event->link;

            $events[] = new Event($title, $date, $link);
        }

        return $events;
    }

    /**
     * Helper method to extract and convert the date from the given event title.
     *
     * @param string $title
     * @return string
     */
    private function extractDate($title)
    {
        if (1 === preg_match('#\d{1,2}\.\d{1,2}\.\d{4}#', $title, $matches)) {
            if (isset($matches[0])) {
                $date = explode('.', $matches[0]);
                return $date[2] . '-' . $date[1] . '-' . $date[0];
            }
        }

        return '';
    }
}
