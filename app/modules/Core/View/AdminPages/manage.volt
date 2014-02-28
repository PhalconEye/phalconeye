{#
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
#}

{% extends "layouts/admin.volt" %}

{% block title %}{{ 'Pages' |i18n }}{% endblock %}

{% block head %}
    <script type="text/javascript">
    var currentLayoutType = '{{currentPage.layout}}';
    var currentPageId = '{{currentPage.id}}';
    var notSaved = false;
    var bundlesWidgetsMetadata = [];
    var widgetsListData = [];
    var elementIdCounter = 1;

    window.onload = function () {
        bundlesWidgetsMetadata = $.parseJSON('{{bundlesWidgetsMetadata}}');
        widgetsListData = $.parseJSON('{{widgetsListData}}');


        if (currentPageId == -1) {
            $("#remove-button").remove();
        }

        window.onbeforeunload = function () {
            if (notSaved)
                return "{{ "Page not saved! Dou you want to leave?" |i18n}}";
        };

        buildWidgetsList();

        $('.widget').draggable({
            helper: 'clone',
            connectToSortable: ".widgets_placer"
        });


        changeCurrentLayoutType(currentLayoutType);
        setWidgetsList({{currentPageWidgets}}, true);

        $(".widget_tooltip").tooltip({
            placement: "left"
        });

        $("#form_page").change(function () {
            $("#form_page").parent().submit();
        });
    };

    var defaultWidgetControl = function (widget, hideEdit) {
        var editLink = '<a href="javascript:;" onclick="editAction($(this));" widget_index="' + widget.widget_index + '" widget_id="' + widget.widget_id + '">{{ "Edit" |i18n}}</a>&nbsp;|';
        if (hideEdit) {
            editLink = '';
        }

        return '<div style="display: block;" class="delete_widget to_remove">' + editLink + '&nbsp;<a href="javascript:;"  onclick="removeAction($(this));">X</a></div>';
    };

    var buildWidgetsList = function () {
        $.each(bundlesWidgetsMetadata, function (i, l) {
            $("#widget_list ul").append('<li class="widget_seperator">' + i + '</li>');
            $.each(l, function (i, l) {
                $("#widget_list ul").append('<li title="' + l.description + '" class="widget_tooltip widget" widget_id="' + l.widget_id + '" widget="' + l.name + '">' + l.name + defaultWidgetControl(l) + '</li>');
            });
            $("#widget_list ul").find('.delete_widget').css('display', 'none');
        });
    };

    var setEditedWidgetIndex = function (index) {
        $("#widget_editing").attr('widget_index', index);
        $("#widget_editing a[widget_index='undefined']").attr('widget_index', index);

        $("#widget_editing").attr("id", "");
        changePageState(true);
    };

    var savePage = function () {
        if (!notSaved) return;

        $.post("{{ url(['for':'admin-pages-save-layout'])}}{{currentPage.id}}", {
            format: "json",
            layout: currentLayoutType,
            items: getWidgetsList(true)
        }, function () {
            changePageState(false);
            window.location.reload();
        })
                .fail(function () {
                    changePageState(true);
                    alert("{{ 'Error while saving...' |i18n }}");
                });

    };

    var editAction = function (element) {
        if ($('#widget_editing'))
            $('#widget_editing').attr('id', '');

        element.parent().parent().attr('id', 'widget_editing');

        var url = '{{ url(['for':'admin-pages-widget-options'])}}';
        var data = {
            'widget_index': parseInt(element.attr('widget_index')),
            'widget_id': element.attr('widget_id'),
            "layout": element.parent().attr("layout")
        };

        PhalconEye.widget.modal.open(url, data);
    };

    var removeAction = function (element) {
        element.parent().parent().remove();
        changePageState(true);
    };

    var changePageState = function (state) {

        if (state) {
            $('#save_button').attr("disabled", null);
            $('#save_button').html("{{"Save (NOT  SAVED)" |i18n}}");
        }
        else {
            $('#save_button').attr("disabled", "disabled");
            $('#save_button').html("{{"Save" |i18n}}");
        }
        $('#save_button').button('reset');
        notSaved = state;
    };

    var bindDraggable = function () {
        $(".widgets_placer").sortable({
            connectWith: '.widgets_placer',
            start: function (event, ui) {
                changePageState(true);

                if (!$(ui.item[0]).attr('element_id'))
                    $(ui.item[0]).attr('element_id', elementIdCounter++);
            },
            receive: function (event, ui) {
                $(".admin_pages_layout").find('.delete_widget').css('display', 'block');
                updateLayoutPanelsHeight();
            }
        });

        $("ul, li").disableSelection();
    };

    var getWidgetsList = function ($no_content) {
        var items = [];

        $(".widgets_placer").each(function () {
            $(this).find(".widget").each(function () {
                items.push({
                    "content": (!$no_content ? $(this).html().trim() : ''),
                    "widget_index": parseInt($(this).attr("widget_index")),
                    "widget_id": $(this).attr('widget_id'),
                    "layout": $(this).parent().attr("layout")
                });
            });
        });

        return items;
    };

    var setWidgetsList = function (list, is_initial) {
        if (!is_initial) {
            var hasRemove = false;

            $.each(list, function (i, l) {
                if ($("#widgets_container_" + l.layout).length > 0) {
                    $("#widgets_container_" + l.layout).append('<li element_id="' + elementIdCounter + '" class="widget" widget_index="' + l.widget_index + '" widget_id="' + l.widget_id + '">' + l.content + '</div>');
                    elementIdCounter++;
                }
                else hasRemove = true;
            });

            return hasRemove;
        }
        else {
            list = JSON.parse(JSON.stringify(list));
            $.each(list, function (i, l) {
                if ($("#widgets_container_" + l.layout).length > 0) {
                    var hideLink = false;

                    // Get widget real title.
                    if (widgetsListData[l.widget_id]) {
                        var title = widgetsListData[l.widget_id].name;
                    }
                    else {
                        hideLink = true;
                        var title = "<b style='color: red;'>{{ "NOT FOUND" |i18n}}</b>";
                    }
                    $("#widgets_container_" + l.layout).append('<li element_id="' + elementIdCounter + '" class="widget" widget_index="' + l.widget_index + '" widget_id="' + l.widget_id + '">' + title + defaultWidgetControl(l, hideLink) + '</div>');
                    elementIdCounter++;
                }
            });
        }
    };


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

    };

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
        var types = type.split('_');

        //getting existing widgets
        if (!widgetsList)
            widgetsList = getWidgetsList();

        // removing existing placers
        $("#global_placer").html('');

        // header
        $("#global_placer").append('<div class="admin_pages_layout layout_header"><span>{{ "Header" |i18n}}</span></div>');

        // adding new placers
        $.each(types, function (i, l) {
            $("#global_placer").append('<ul layout="' + l + '" id="widgets_container_' + l + '" class="admin_pages_layout widgets layout_' + l + ' widgets_placer"></ul>');
        });

        // footer
        $("#global_placer").append('<div class="admin_pages_layout layout_footer"><span>{{ "Footer" |i18n}}</span></div>');


        // correcting middle placer
        if ($("#widgets_container_middle")) {
            if ($.inArray('left', types) != -1 && $.inArray('right', types) != -1) {
                if (window.opera || !!window.ActiveXObject) // opera or ie 7-8
                    $("#widgets_container_middle").attr("style", "width: 365px !important");
                else
                    $("#widgets_container_middle").attr("style", "width: 366px !important");
            }
            else if ($.inArray('left', types) != -1 || $.inArray('right', types) != -1) {
                if (window.opera || !!window.ActiveXObject)
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
            if (!confirm("{{ "If you switch to new layout you will lose some widgets, are you shure?" |i18n}}")) {
                changeCurrentLayoutType(currentLayoutType, widgetsList);
                return;
            }
        }

        bindDraggable();
        if (currentLayoutType != type) {
            changePageState(true);
            currentLayoutType = type;
        }
    }


    </script>
{% endblock %}

{% block header %}
    <div class="navbar navbar-header">
        <div class="navbar-inner">
            {{ navigation.render() }}
        </div>
    </div>
{% endblock %}

{% block content %}
    <div class="span12 row-page-manager">

        <div class="manage_page_header">
            <div class="manage_page_header_label">
                <h3>
                    <a href="{{ url(['for':'admin-pages']) }}">{{ "Pages" |i18n }}</a>
                    > {{ "Manage page" |i18n }}</h3>
                <a {% if currentPage.type is null and currentPage.url is not null %}href="/page/{{ currentPage.url }}"
                   target="_blank" {% else %} href="javascript:;"{% endif %}
                        >{{ currentPage.title }}</a>
            </div>

            <div class="widget_options_panel">

                <div class="btn-group">
                    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                        {{ "Change layout" |i18n }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div id="layout_select_block">
                                <div class="admin_layoutbox_menu_columnchoices_instructions">
                                    {{ "Select layout type for current page" |i18n }}
                                </div>
                                <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_RIGHT_MIDDLE_LEFT')~'.png') }}"
                                             alt="3 columns"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_RIGHT_MIDDLE_LEFT') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_MIDDLE_LEFT')~'.png') }}"
                                             alt="2 columns - Left"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_MIDDLE_LEFT') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_RIGHT_MIDDLE')~'.png') }}"
                                             alt="2 columns - Right"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_RIGHT_MIDDLE') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_MIDDLE')~'.png') }}"
                                             alt="1 columns"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_MIDDLE') }}');">
                                    </li>
                                </ul>
                                <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_TOP_RIGHT_MIDDLE_LEFT')~'.png') }}"
                                             alt="3 columns"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_TOP_RIGHT_MIDDLE_LEFT') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_TOP_MIDDLE_LEFT')~'.png') }}"
                                             alt="2 columns - Left"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_TOP_MIDDLE_LEFT') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_TOP_RIGHT_MIDDLE')~'.png') }}"
                                             alt="2 columns - Right"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_TOP_RIGHT_MIDDLE') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_TOP_MIDDLE')~'.png') }}"
                                             alt="1 columns"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_TOP_MIDDLE') }}');">
                                    </li>
                                </ul>
                                <ul class="admin_layoutbox_menu_columnchoices_thumbs">
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_RIGHT_MIDDLE_LEFT_BOTTOM')~'.png') }}"
                                             alt="3 columns"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_RIGHT_MIDDLE_LEFT_BOTTOM') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_MIDDLE_LEFT_BOTTOM')~'.png') }}"
                                             alt="2 columns - Left"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_MIDDLE_LEFT_BOTTOM') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_RIGHT_MIDDLE_BOTTOM')~'.png') }}"
                                             alt="2 columns - Right"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_RIGHT_MIDDLE_BOTTOM') }}');">
                                    </li>
                                    <li>
                                        <img src="{{ url('assets/img/core/admin/content/'~constant('\Core\Model\Page::LAYOUT_MIDDLE_BOTTOM')~'.png') }}"
                                             alt="1 columns"
                                             onclick="changeCurrentLayoutType('{{ constant('\Core\Model\Page::LAYOUT_MIDDLE_BOTTOM') }}');">
                                    </li>
                                </ul>


                            </div>
                        </li>
                    </ul>
                </div>
                <button id="save_button" disabled="disabled" onclick="savePage();" type="button"
                        class="btn btn-primary button-loading"
                        data-loading-text="{{ "Saving..." |i18n }}">{{ "Save" |i18n }}</button>
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
{% endblock %}
