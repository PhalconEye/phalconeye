<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Core\Model;

use Engine\Package\Model\AbstractPackageDependency;

/**
 * Package dependency.
 *
 * @category  PhalconEye
 * @package   Core\Model
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 *
 * @Source("package_dependencies")
 * @BelongsTo("package_id", "Core\Model\PackageModel", "id")
 * @BelongsTo("dependency_id", "Core\Model\PackageModel", "id", {
 *  "alias": "DependencyModel"
 * })
 */
class PackageDependencyModel extends AbstractPackageDependency
{
}
