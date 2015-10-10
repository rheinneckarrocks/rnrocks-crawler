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

use RNRocks\Usergroup;

interface UsergroupRepository
{
    /**
     * Returns a list of all available usergroups.
     *
     * @return Usergroup[]
     */
    public function getUsergroups();
}
