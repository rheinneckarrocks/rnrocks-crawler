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
use Symfony\Component\Yaml\Parser;

class SculpinUsergroupRepository implements UsergroupRepository
{
    /**
     * @var string
     */
    protected $folder;
    /**
     * @var Parser
     */
    protected $yamlParser;

    /**
     * Creates a new {@link \RNRocks\Repository\SculpinUsergroupRepository}.
     *
     * @param string $folder
     */
    public function __construct($folder)
    {
        $this->folder = $folder;
        $this->yamlParser = new Parser();
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
            if (false !== strpos($file->getBasename(), '.html')) {
                $content = file_get_contents($file->getRealPath());
                if (empty($content)) {
                    continue;
                }
                
                // "extract" the YAML Parts from the input file
                $content = explode('---', $content);
                if (!isset($content[1]) || count($content) !== 3) {
                    continue;
                }

                $content = $content[1];
                $values = $this->yamlParser->parse($content);

                $slug = isset($values['title']) ? $values['title'] : null;
                $type = isset($values['eventFeed'], $values['eventFeed']['type']) ? $values['eventFeed']['type'] : null;
                $eventLink = isset($values['eventFeed'], $values['eventFeed']['url']) ? $values['eventFeed']['url'] : null;
                if (null === $slug || null === $type || null === $eventLink) {
                    continue;
                }

                $usergroups[] = new Usergroup($slug, $type, $eventLink);
            }
        }

        return $usergroups;
    }
}
