{% extends "layouts/admin.volt" %}

{% block title %}{{ 'Pages' | trans }}{% endblock %}

{% block head %}
    <script type="text/javascript">
    //var currentLayoutType =  'top,right,middle,left,bottom';
    var currentLayoutType = '{{currentPage.getLayout()}}';
    var currentPageId = '{{currentPage.getId()}}';
    var notSaved = false;
    var bundlesWidgetsMetadata = [];
    var widgetsListData = [];

    window.onload = function () {
        bundlesWidgetsMetadata = $.parseJSON('{{bundlesWidgetsMetadata}}');
        widgetsListData = $.parseJSON('{{widgetsListData}}');


        if (currentPageId == -1) {
            $("#remove-button").remove();
        }

        window.onbeforeunload = function () {
            if (notSaved)
                return "{{ "Page not saved! Dou you want to leave?" | trans}}";
        };

        buildWidgetsList();

        $('.widget').draggable({
            helper:'clone',
            connectToSortable:".widgets_placer"
        });


        changeCurrentLayoutType(currentLayoutType);
        setWidgetsList('{{currentPageWidgets}}', true);
        admin.modal.init('.admin_pages_layout [data-toggle="modal"]');

        $(".widget_tooltip").tooltip({
            position:"center left"
        });

        $("#form_page").change(function () {
            $("#form_page").parent().submit();
        });
    };

    var defaultWidgetControl = function (widget) {
        var path = '/admin/pages/options?id=widget_id&name=widget_name&page_id=widget_page_id';
        path = path.replace("widget_id", widget.id).replace("widget_name", widget.name).replace("widget_page_id", currentPageId);
        return   '<div style="display: block;" class="delete_widget to_remove"><a href="' + path + '" onclick="if ($(\'#widget_editing\')) $(\'#widget_editing\').attr(\'id\', \'\'); $(this).parent().parent().attr(\'id\', \'widget_editing\');"  data-toggle="modal">{{ "Edit" | trans}}</a>&nbsp;|&nbsp;<a href="javascript:;"  onclick="$(this).parent().parent().remove(); changePageState(true);">X</a></div>';
    }

    var buildWidgetsList = function () {
        $.each(bundlesWidgetsMetadata, function (i, l) {
            $("#widget_list ul").append('<li class="widget_seperator">' + i + '</li>');
            $.each(l, function (i, l) {
                $("#widget_list ul").append('<li title="' + l.description + '" class="widget_tooltip widget" widgetid="0" widget="' + l.name + '">' + l.name + defaultWidgetControl(l) + '</li>');
            });
            $("#widget_list ul").find('.delete_widget').css('display', 'none');
        });
    }

    var setEditedWidgetId = function (id) {
        $("#widget_editing").attr("widgetid", id);

        var href = $("#widget_editing").find(".thickbox").attr("href");
        href = href.replace("undefined", id);
        $("#widget_editing").find(".thickbox").attr("href", href);

        $("#widget_editing").attr("id", "");
    }

    var savePage = function () {
        if (!notSaved) return;

        $.getJSON("/admin/pages/save-layout/{{currentPage.getId()}}",
                {
                    format:"json",
                    layout:currentLayoutType,
                    items:getWidgetsList(true)
                },
                function (data) {
                    changePageState(false);
                });
    }


    var changePageState = function (state) {
        if (state)
            $('#save_button').val("{{"Save (NOT  SAVED)" | trans}}");
        else
            $('#save_button').val("{{"Save" | trans}}");
        notSaved = state;
    }

    var bindDraggable = function () {
        $(".widgets_placer").sortable({
            connectWith:'.widgets_placer',
            start:function (event, ui) {
                changePageState(true);
            },
            receive:function (event, ui) {
                $(".admin_pages_layout").find('.delete_widget').css('display', 'block');
                admin.modal.init('.admin_pages_layout [data-toggle="modal"]');
                updateLayoutPanelsHeight();
            }
        });

        $("ul, li").disableSelection();
    }

    var getWidgetsList = function ($no_content) {
        var items = [];

        $(".widgets_placer").each(function () {
            $(this).find(".widget").each(function () {
                items.push({
                    "content":(!$no_content ? $(this).html().trim() : ''),
                    "id":$(this).attr("widgetid"),
                    "name":$(this).attr("widget"),
                    "layout":$(this).parent().attr("layout")
                });
            });
        });

        return items;
    }

    var setWidgetsList = function (list, is_initial) {
        if (!is_initial) {
            var hasRemove = false;

            $.each(list, function (i, l) {
                if ($("#widgets_container_" + l.layout).length > 0) {
                    $("#widgets_container_" + l.layout).append('<li class="widget" widgetid="' + l.id + '" widget="' + l.name + '">' + l.content + '</div>');
                }
                else hasRemove = true;
            });

            return hasRemove;
        }
        else {
            list = $.parseJSON(list);
            $.each(list, function (i, l) {
                if ($("#widgets_container_" + l.layout).length > 0) {
                    // get widget real title
                    if (widgetsListData[l.name])
                        var title = widgetsListData[l.name].title;
                    else
                        var title = "<b style='color: red;'>{{ "NOT FOUND" | trans}}</b>";
                    $("#widgets_container_" + l.layout).append('<li class="widget" widgetid="' + l.id + '" widget="' + l.name + '">' + title + defaultWidgetControl(l) + '</div>');
                }
            });
        }
    }


    var updateLayoutPanelsHeight = function () {
        // get max height;
        var maxHeight = 0;

        if ($("#widgets_container_middle")) if (maxHeight < $("#widgets_container_middle").height()) maxHeight = $("#widgets_container_middle").height();
        if ($("#widgets_container_left")) if (maxHeight < $("#widgets_container_left").height()) maxHeight = $("#widgets_container_left").height();
        if ($("#widgets_container_right")) if (maxHeight < $("#widgets_container_right").height()) maxHeight = $("#widgets_container_right").height();

        // setting to all height{
        if ($("#widgets_container_middle")) $("#widgets_container_middle").css("min-height", maxHeight + "px");
        if ($("#widgets_container_left")) $("#widgets_container_left").css("min-height", maxHeight + "px");
        if ($("#widgets_container_right")) $("#widgets_container_right").css("min-height", maxHeight + "px");

    }

    var changeCurrentLayoutType = function (type, widgetsList) {
        // Header or Footer
        if (type == "special") {
            $("#change-layout-button").remove();
            $("#remove-button").remove();

            $("#global_placer").append('<ul layout="special" id="widgets_container_special" class="admin_pages_layout widgets layout_middle widgets_placer"></ul>');
            $("#widgets_container_special").css("width", "759px !important");
            bindDraggable();

            return;
        }

        // Normal pages
        var types = type.split(',');

        //getting existing widgets
        if (!widgetsList)
            widgetsList = getWidgetsList();

        // removing existing placers
        $("#global_placer").html('');

        // header
        $("#global_placer").append('<div class="admin_pages_layout layout_header"><span>{{ "Header" | trans}}</span></div>');

        // adding new placers
        $.each(types, function (i, l) {
            $("#global_placer").append('<ul layout="' + l + '" id="widgets_container_' + l + '" class="admin_pages_layout widgets layout_' + l + ' widgets_placer"></ul>');
        });

        // footer
        $("#global_placer").append('<div class="admin_pages_layout layout_footer"><span>{{ "Footer" | trans}}</span></div>');


        // correcting middle placer
        if ($("#widgets_container_middle")) {
            if (types.indexOf('left') != -1 && types.indexOf('right') != -1) {
                if (window.opera)
                    $("#widgets_container_middle").attr("style", "width: 365px !important");
                else
                    $("#widgets_container_middle").attr("style", "width: 366px !important");
            }
            else if (types.indexOf('left') != -1 || types.indexOf('right') != -1) {
                if (window.opera)
                    $("#widgets_container_middle").attr("style", "width: 549px !important");
                else
                    $("#widgets_container_middle").attr("style", "width: 550px !important");
            }
            else {
                $("#widgets_container_middle").attr("style", "width: 734px !important");
            }
        }

        // setting widgets list
        var hasRemove = setWidgetsList(widgetsList);

        if (hasRemove) {
            if (!confirm("{{ "If you switch to new layout you will lose some widgets, are you shure?" | trans}}")) {
                changeCurrentLayoutType(currentLayoutType, widgetsList);
                return;
            }
        }

        bindDraggable();
        if (currentLayoutType != type) {
            changePageState(true);
            currentLayoutType = type;
        }
        admin.modal.init('.admin_pages_layout [data-toggle="modal"]');
    }


    </script>
{% endblock %}

{% block content %}

    <div class="row-fluid">
        <div class="manage_page_header">
            <div class="manage_page_header_label">
                <h3><a href="/admin/pages">{{ "<< Back" | trans }}</a> | {{ "Manage page" | trans }}</h3>
                <a href="/{{ currentPage.getUrl() }}" target="_blank">{{ currentPage.getTitle() }}</a>
            </div>

            <div class="widget_options_panel">

                <div class="btn-group">
                    <a class="btn btn-inverse dropdown-toggle" data-toggle="dropdown" href="#">
                        {{ "Change layout" | trans }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div id="layout_select_block">
                                <div class="admin_layoutbox_menu_columnchoices_instructions">
                                    {{ "Select layout type for current page" | trans }}
                                </div>
                                <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                                    <li>
                                        <img src="/public/img/admin/content/cols1_3.png" alt="3 columns"
                                             onclick="changeCurrentLayoutType('right,middle,left');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols1_2left.png" alt="2 columns - Left"
                                             onclick="changeCurrentLayoutType('middle,left');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols1_2right.png" alt="2 columns - Right"
                                             onclick="changeCurrentLayoutType('right,middle');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols1_1.png" alt="1 columns"
                                             onclick="changeCurrentLayoutType('middle');">
                                    </li>
                                </ul>
                                <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                                    <li>
                                        <img src="/public/img/admin/content/cols2_3.png" alt="3 columns"
                                             onclick="changeCurrentLayoutType('top,right,middle,left');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols2_2left.png" alt="2 columns - Left"
                                             onclick="changeCurrentLayoutType('top,middle,left');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols2_2right.png" alt="2 columns - Right"
                                             onclick="changeCurrentLayoutType('top,right,middle');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols2_1.png" alt="1 columns"
                                             onclick="changeCurrentLayoutType('top,middle');">
                                    </li>
                                </ul>
                                <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                                    <li>
                                        <img src="/public/img/admin/content/cols3_3.png" alt="3 columns"
                                             onclick="changeCurrentLayoutType('right,middle,left,bottom');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols3_2left.png" alt="2 columns - Left"
                                             onclick="changeCurrentLayoutType('middle,left,bottom');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols3_2right.png" alt="2 columns - Right"
                                             onclick="changeCurrentLayoutType('right,middle,bottom');">
                                    </li>
                                    <li>
                                        <img src="/public/img/admin/content/cols3_1.png" alt="1 columns"
                                             onclick="changeCurrentLayoutType('middle,bottom');">
                                    </li>
                                </ul>


                            </div>
                        </li>
                    </ul>
                </div>
                <button type="button" class="btn btn-primary button-loading" data-loading-text="{{ "Saving..." | trans }}">{{ "Save" | trans }}</button>
            </div>
        </div>
        <div class="clearfix"></div>

        <div id="widget_list" class="admin_pages_widgets">
            <ul class="widgets">

            </ul>

        </div>

        <div id="global_placer" class="admin_pages_layout">


        </div>
        <div class="clear"></div>

    </div>
    <!--/row-->

{% endblock %}
