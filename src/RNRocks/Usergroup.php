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

class Usergroup
{
    /**
     * @var string
     */
    protected $slug;
    /**
     * @var string
     */
    protected $repoType;
    /**
     * @var string
     */
    protected $eventLink;

    /**
     * Creates a new {@link \RNRocks\Usergroup}.
     *
     * @param $slug
     * @param string $repoType
     * @param string $eventLink
     */
    public function __construct($slug, $repoType, $eventLink)
    {
        $this->slug = $slug;
        $this->repoType = $repoType;
        $this->eventLink = $eventLink;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getRepoType()
    {
        return $this->repoType;
    }

    /**
     * @return string
     */
    public function getEventLink()
    {
        return $this->eventLink;
    }
}
