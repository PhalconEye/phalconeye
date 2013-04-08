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

