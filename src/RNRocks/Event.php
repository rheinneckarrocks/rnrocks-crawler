<?php

/*
 * This file is part of the Rhein Neckar Rocks Crawler project.
 *
 * (c) Rhein Neckar Rocks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RNRocks;

class Event
{
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $date;
    /**
     * @var string
     */
    protected $location;
    /**
     * @var string
     */
    protected $link;

    /**
     * Creates a new {@link \RNRocks\Event}.
     *
     * @param string $title
     * @param string $date
     * @param string $link
     * @param string $location
     */
    public function __construct($title, $date, $link, $location = 'tdb.')
    {
        $this->title = $title;
        $this->date = $date;
        $this->link = $link;
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }
}
