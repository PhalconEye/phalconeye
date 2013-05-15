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

/**
 * Sluggable trait.
 *
 * Should be used inside entities for which slugs should automatically be generated on creation for SEO/permalinks.
 */
trait Sluggable
{
    private $slug;

    public function beforeCreate()
    {
        $this->generateSlug();
    }

    public function beforeUpdate()
    {
        if ($this->getRegenerateSlugOnUpdate() || empty($this->slug)) {
            $this->generateSlug();
        }
    }

    /**
     * Returns the slug's delimiter
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
     * Returns the entity's slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Generates and sets the entity's slug. Called prePersist and preUpdate
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
            throw new \Engine\Exception('Sluggable expects to have at least one usable (non-empty) field from the following: [ ' . implode($fields, ',') . ' ]');
        }

        // generate the slug itself
        $sluggableText = implode($usableValues, ' ');
        $urlized = strtolower(trim(preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', iconv('UTF-8', 'ASCII//TRANSLIT', $sluggableText)), $this->getSlugDelimiter()));
        $urlized = preg_replace("/[\/_|+ -]+/", $this->getSlugDelimiter(), $urlized);

        $this->slug = $urlized;
    }
}
