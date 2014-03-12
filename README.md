Phalcon Eye CMS
=====================

Phalcon Eye - CMS based on Phalcon PHP Framework (https://github.com/phalcon/cphalcon).

* Version: 0.4.0
* Status: In Testing...
* Requirements: PHP >= 5.4.x, Phalcon = 1.3.0, zlib, mbstring, mcrypt, iconv, gd, fileinfo, zip
* Website: http://phalconeye.com/

Installation
------------
1. Get source.
2. If you cloned it from github you can run ant task (ant dist) and get package as zip.
3. Unzip (or copy) CMS code to your webserver.
4. 'public' directory must be set as server's web root.
5. Go to http://youhost.com/ and you will see the installation process.
6. Follow the installation process.

Note: If you want to reinstall, set option 'installed' to 'false' in /app/var/data/app.php.

Coding Style [![Build Status](https://secure.travis-ci.org/lantian/PhalconEye.png?branch=master)](http://travis-ci.org/lantian/PhalconEye)
------------
PhalconEye CMS code style is checked via Travis CI service. Every commit pushed to this repository will queue a build
into the continuous integration service to run phpmd and phpcs checks.

Third Party
-----------
* jQuery: https://jquery.org/ (MIT)
* CKEditor: http://ckeditor.com/ (GPL, LGPL and MPL)
* AjaXplorer: http://ajaxplorer.info/ (GPL)
* lessphp: http://leafo.net/lessphp/ (GPL3/MIT)

License
-------
Phalcon Eye CMS is open-source software licensed under the New BSD License. See the LICENSE.txt file for more information.

