
/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2013 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

var PrettyExceptions = {

	/**
	 * Initialize the pretty print and highlights lines on files
	 */
	initialize: function()
	{

		prettyPrint();

		var prettyprint = $(".prettyprint");
		for (var i = 0; i < prettyprint.length; i++) {
			$($(prettyprint[i]).attr("class").split(" ")).each(
				function(k, v) {
					if (v.match("^highlight")) {
						var lis = $("li", prettyprint[i]);
						var position = Math.abs(v.split(":")[2] - v.split(":")[1]);
						var li = lis[position];
						$(li).addClass("highlight");
						for (var j = position - 10; j < lis.length; j++) {
							if (j >= 0) {
								$(prettyprint[i]).scrollTo(lis[j]);
								break;
							}
						}
					}
				}
			);
		}
	}

};

$(document).ready(function() {
	PrettyExceptions.initialize();
});