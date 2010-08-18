<?php
/**
 * @package getpage
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'namespace',
        'desc' => 'An execution namespace that serves as a prefix for placeholders set by a specific instance of the getPage snippet.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    )
    ,array(
        'name' => 'limit',
        'desc' => 'The result limit per page; can be overridden in the $_REQUEST.',
        'type' => 'textfield',
        'options' => '',
        'value' => '10',
    )
    ,array(
        'name' => 'offset',
        'desc' => 'The offset, or record position to start at within the collection for rendering results for the current page; should be calculated based on page variable set in pageVarKey.',
        'type' => 'textfield',
        'options' => '',
        'value' => '0',
    )
    ,array(
        'name' => 'page',
        'desc' => 'The page to display; this is determined based on the value indicated by the page variable set in pageVarKey, typically in the $_REQUEST.',
        'type' => 'textfield',
        'options' => '',
        'value' => '0',
    )
    ,array(
        'name' => 'pageVarKey',
        'desc' => 'The key of a property that indicates the current page.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'page',
    )
    ,array(
        'name' => 'totalVar',
        'desc' => 'The key of a placeholder that must contain the total records in the limitable collection being paged.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'total',
    )
    ,array(
        'name' => 'pageLimit',
        'desc' => 'The maximum number of pages to display when rendering paging controls',
        'type' => 'textfield',
        'options' => '',
        'value' => '5',
    )
    ,array(
        'name' => 'elementClass',
        'desc' => 'The class of element that will be called by the getPage snippet instance.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'modSnippet',
    )
    ,array(
        'name' => 'pageNavVar',
        'desc' => 'The key of a placeholder to be set with the paging navigation controls.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'page.nav',
    )
    ,array(
        'name' => 'pageNavTpl',
        'desc' => 'Content representing a single page navigation control.',
        'type' => 'textfield',
        'options' => '',
        'value' => '<li[[+classes]]><a[[+classes]][[+title]] href="[[+href]]">[[+pageNo]]</a></li>',
    )
    ,array(
        'name' => 'pageActiveTpl',
        'desc' => 'Content representing the current page navigation control.',
        'type' => 'textfield',
        'options' => '',
        'value' => '<li[[+activeClasses]]><a[[+activeClasses:default=` class="active"`]][[+title]] href="[[+href]]">[[+pageNo]]</a></li>',
    )
    ,array(
        'name' => 'pageFirstTpl',
        'desc' => 'Content representing the first page navigation control.',
        'type' => 'textfield',
        'options' => '',
        'value' => '<li class="control"><a[[+classes]][[+title]] href="[[+href]]">First</a></li>',
    )
    ,array(
        'name' => 'pageLastTpl',
        'desc' => 'Content representing the last page navigation control.',
        'type' => 'textfield',
        'options' => '',
        'value' => '<li class="control"><a[[+classes]][[+title]] href="[[+href]]">Last</a></li>',
    )
    ,array(
        'name' => 'pagePrevTpl',
        'desc' => 'Content representing the previous page navigation control.',
        'type' => 'textfield',
        'options' => '',
        'value' => '<li class="control"><a[[+classes]][[+title]] href="[[+href]]">&lt;&lt;</a></li>',
    )
    ,array(
        'name' => 'pageNextTpl',
        'desc' => 'Content representing the next page navigation control.',
        'type' => 'textfield',
        'options' => '',
        'value' => '<li class="control"><a[[+classes]][[+title]] href="[[+href]]">&gt;&gt;</a></li>',
    )
);

return $properties;