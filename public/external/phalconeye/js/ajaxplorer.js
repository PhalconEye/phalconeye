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

var PE = PE || {};

PE.ajaxplorer = {
    currentElement:null,

    openAjaxplorerPopup:function(element, url, title){
        currentElement = element;
        window.open(url, title, 'width=800,height=600,resizable=yes,scrollbars=yes,status=yes').focus();
    },

    ajaxplorerPopupCallback:function(data){
        if(typeof(data) === "string" && currentElement){
            currentElement.find('input[type="text"]').val(data);
        }
        currentElement = null;
    }
};

