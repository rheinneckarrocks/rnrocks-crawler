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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
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
     * @param $oauth2Key
     * @param $oauth2Secret
     */
    public function __construct($oauth2Key, $oauth2Secret)
    {
        // boilerplate code for Oauth2
        $oAuth2Client = new Client([
            // URL for access_token request
            'base_uri' => 'https://secure.meetup.com/oauth2/access',
        ]);

        $oAuth2Config = [
            'client_id' => $oauth2Key,
            'client_secret' => $oauth2Secret,
        ];
        $clientCredentials = new ClientCredentials($oAuth2Client, $oAuth2Config);
        $oAuth2Middleware = new OAuth2Middleware($clientCredentials);

        $this->client = new Client();
        $this->client->getConfig('handler')->push($oAuth2Middleware);
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

        try {
            $response = $this->client->request('GET', sprintf('https://api.meetup.com/%s/events', $groupUrlName));
            if($response->getStatusCode() === 200) {
                $eventData = json_decode($response->getBody()->getContents());
                if(is_array($eventData)) {
                    foreach ($eventData as $event) {
                        $event = (array) $event;
                        $date = date('Y-m-d', $event['time'] / 1000);
                        $venue = null;
                        if (isset($event['venue'])) {
                            $event['venue'] = (array) $event['venue'];
                            if (isset($event['venue']['name']) && isset($event['venue']['city'])) {
                                $venue = $event['venue']['name'] . ', ' . $event['venue']['city'];
                            }
                        }

                        $events[] = new Event($event['name'], $date, $event['link'], $venue);
                    }
                }
            }
        } catch (GuzzleException $e) {
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
