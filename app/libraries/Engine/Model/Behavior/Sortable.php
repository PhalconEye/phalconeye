<?php

/**
 * PhalconEye
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to phalconeye@gmail.com so we can send you a copy immediately.
 *
 */

namespace Engine\Model\Behavior;

trait Sortable
{
    private $sort = 1;

    private $reordered = false;

    /**
     * Get sort.
     *
     * @return sort.
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set sort.
     *
     * @param sort the value to set.
     */
    public function setSort($sort)
    {
        $this->reordered = $this->sort !== $sort;
        $this->sort      = $sort;
    }

    public function isReordered()
    {
        return $this->reordered;
    }

    public function setReordered()
    {
        $this->reordered = true;
    }
}
