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

use DMS\Service\Meetup\MeetupKeyAuthClient;
use RNRocks\Event;

class MeetupEventRepository implements EventRepository
{
    /**
     * @var MeetupKeyAuthClient
     */
    protected $client;

    /**
     * Creates a new {@link \RNRocks\Repository\MeetupEventRepository}.
     *
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->client = MeetupKeyAuthClient::factory(array('key' => $apiKey));
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents($url)
    {
        $events = [];

        $groupUrlName = $this->extractGroupNameFromUrl($url);
        if (empty($groupUrlName)) {
            return $events;
        }

        $response = $this->client->getEvents(['group_urlname' => $groupUrlName]);
        foreach ($response->getData() as $event) {
            $date = date('Y-m-d', $event['time'] / 1000);
            $venue = null;
            if (isset($event['venue']) && isset($event['venue']['name']) && isset($event['venue']['city'])) {
                $venue = $event['venue']['name'] . ', ' . $event['venue']['city'];
            }

            $events[] = new Event($event['name'], $date, $event['event_url'], $venue);
        }

        return $events;
    }

    /**
     * Helper method to extract the group name from the given url.
     *
     * @param string $url
     * @return string
     */
    private function extractGroupNameFromUrl($url)
    {
        $urlPath = parse_url(trim($url, '/'), PHP_URL_PATH);
        $urlPath = explode('/', $urlPath);
        $groupUrlName = array_pop($urlPath);
        return $groupUrlName;
    }
}
