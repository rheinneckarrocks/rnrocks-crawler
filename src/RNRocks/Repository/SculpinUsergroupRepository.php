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

use DirectoryIterator;
use RNRocks\Usergroup;

class SculpinUsergroupRepository implements UsergroupRepository
{
    /**
     * @var string
     */
    protected $folder;

    /**
     * Creates a new {@link \RNRocks\Repository\SculpinUsergroupRepository}.
     *
     * @param string $folder
     */
    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsergroups()
    {
        $usergroups = [];

        $iterator = new DirectoryIterator($this->folder);
        foreach($iterator as $file) {
            /** @var DirectoryIterator $file */
            if (false !== strpos($file->getBasename(), '.md')) {
                $content = file_get_contents($file->getRealPath());
                if (false !== preg_match_all('#^([a-zA-Z0-9]+?):\W+(.+)$#m', $content, $matches, PREG_SET_ORDER))
                {
                    $slug = null;
                    $type = null;
                    $eventLink = null;
                    foreach($matches as $match) {
                        if(!isset($match[1])) {
                            continue;
                        }

                        if((null === $slug) && ($match[1] === 'slug')) {
                            $slug = trim($match[2]);
                        }

                        if((null === $type) && ($match[1] === 'type')) {
                            $type = strtolower(trim($match[2]));
                        }

                        if((null === $eventLink) && ($match[1] === 'eventLink')) {
                            $eventLink = trim($match[2]);
                        }
                    }

                    if (null === $slug || null === $type || null === $eventLink) {
                        continue;
                    }

                    $usergroups[] = new Usergroup($slug, $type, $eventLink);
                }
            }
        }

        return $usergroups;
    }
}
