var PE = PE || {};

PE.core = {
    initButtonLoading:function () {
        $('.button-loading')
            .click(function () {
                var btn = $(this);
                btn.button('loading');
            });
    },

    initAutocomplete:function () {
        var autocomplete = $('.autocomplete').autocomplete({
            source:function (request, response) {
                $.ajax({
                    url:$(this)[0].element[0].dataset.link,
                    type:'get',
                    data:{query:request.term},
                    dataType:'json',
                    success:function (json) {
                        response($.map(json, function (item) {
                            return {
                                label:item.label,
                                value:item.id
                            }
                        }));
                    }
                });
            },
            open:function () {
                $(this).data("autocomplete").menu.element.attr('class', "typeahead dropdown-menu");
            },
            select: function( event, ui ) {
                $(event.target).val( ui.item.label );

                var targetElement = $(event.target)[0].dataset.target;

                if (targetElement){
                    $(targetElement).val(ui.item.value);
                }
                return false;
            }
        }).data("autocomplete");

        if (autocomplete) {
            autocomplete._resizeMenu = function () { // fix position of dropdown
                var ul = this.menu.element;
                ul.outerWidth(this.element.outerWidth());
            }
        }
    }

};


$(document).ready(function () {
    PE.core.initButtonLoading();
    PE.core.initAutocomplete();
    PE.modal.init('[data-toggle="modal"]');
});