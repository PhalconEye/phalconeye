<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine\Db\Model\Behavior;

use Engine\Exception as EngineException;

/**
 * Sluggable behaviour.
 *
 * @category  PhalconEye
 * @package   Engine\Db\Model\Behaviour
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
trait Sluggable
{
    /**
     * @Column(type="string", nullable=false, column="slug", size="255")
     */
    public $slug;

    /**
     * Before entity creation.
     *
     * @return void
     */
    public function beforeCreate()
    {
        $this->generateSlug();
    }

    /**
     * Before entity update.
     *
     * @return void
     */
    public function beforeUpdate()
    {
        if ($this->getRegenerateSlugOnUpdate() || empty($this->slug)) {
            $this->generateSlug();
        }
    }

    /**
     * Returns the slug's delimiter.
     *
     * @return string
     */
    private function getSlugDelimiter()
    {
        return '-';
    }

    /**
     * Returns whether or not the slug gets regenerated on update.
     *
     * @return bool
     */
    private function getRegenerateSlugOnUpdate()
    {
        return true;
    }

    /**
     * Get fields that can be sluggable.
     *
     * @return array
     */
    private function getSluggableFields()
    {
        return ['title'];
    }

    /**
     * Generates and sets the entity's slug. Called prePersist and preUpdate.
     *
     * @throws \Engine\Exception
     * @return void
     */
    public function generateSlug()
    {
        $fields = $this->getSluggableFields();
        $usableValues = [];

        foreach ($fields as $field) {
            // Too bad empty is a language construct...otherwise we could use the return value in a write context :)
            $val = $this->{$field};
            if (!empty($val)) {
                $usableValues[] = $val;
            }
        }

        if (count($usableValues) < 1) {
            throw new EngineException(
                sprintf(
                    'Sluggable expects to have at least one usable (non-empty) field from the following: [%s]',
                    implode($fields, ',')
                )
            );
        }

        // generate the slug itself
        $sluggableText = implode($usableValues, ' ');
        $urlized = strtolower(
            trim(
                preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', iconv('UTF-8', 'ASCII//TRANSLIT', $sluggableText)),
                $this->getSlugDelimiter()
            )
        );
        $urlized = preg_replace("/[\/_|+ -]+/", $this->getSlugDelimiter(), $urlized);

        $this->slug = $urlized;
    }
}
