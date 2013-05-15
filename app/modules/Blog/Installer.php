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

namespace Blog;

class Installer extends \Engine\Installer{

    /**
     * Used to install specific database entities or other specific action
     */
    public function install(){
        // Add dynamic pages
        $page = new \Core\Model\Page();
        $page->title = 'Blog';
        $page->description = 'Blog profile';
        $page->keywords = 'Blog profile';
        $page->url = 'blog-profile';
        $page->layout = 'middle';
        $page->save();
    }

    /**
     * Used before package will be removed from the system
     */
    public function remove(){
        $page = \Core\Model\Page::find(array(
            'conditions' => 'url=:url:',
            'bind' => (array(
                "url" => 'blog-profile'
            )),
            'bindTypes' => (array(
                "url" => \Phalcon\Db\Column::BIND_PARAM_STR
            ))
        ))->getFirst();

        if ($page){
            $page->delete();
        }
    }

    /**
     * Used to apply some updates
     *
     * @param $currentVersion
     * @return mixed 'string' (new version) if migration is not finished, 'null' if all updates were applied
     */
    public function update($currentVersion){
        switch($currentVersion){
            case "1.0.0.1": return $this->_updateTo1002(); break;
            case "1.0.0.2": return $this->_updateTo1003(); break;
        }

        return null;
    }

    private function _updateTo1002(){
        // some update for version "1.0.0.2"

        return "1.0.0.2";
    }

    private function _updateTo1003(){
        // some update for version "1.0.0.3"

        return "1.0.0.3";
    }
}