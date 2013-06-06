<?php
/**
 * @package getpage
 * @subpackage build
 */
$properties = array(
	array(
		'name' => 'namespace',
		'desc' => 'An execution namespace that serves as a prefix for placeholders set by a specific instance of the getPageExt snippet.',
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
		'value' => '2',
	)
	,array(
		'name' => 'elementClass',
		'desc' => 'The class of element that will be called by the getPageExt snippet instance.',
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
		'name' => 'pageNavOuterTpl',
		'desc' => 'Content representing the layout of the page navigation controls.',
		'type' => 'textfield',
		'options' => '',
		'value' => '[[+first]][[+prev]][[+pages]][[+next]][[+last]]',
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
	,array(
		'name' => 'cache',
		'desc' => 'Cache key generation algorithm. Available algorithms: uri, custom, modx',
		'type' => 'textfield',
		'options' => '',
        'value' => 'modx',
	)
	,array(
		'name' => 'cachePageKey',
		'desc' => 'For personalize pagination if you have more one pagination in the current document.',
		'type' => 'textfield',
		'options' => '',
        'value' => '',
	)
	,array(
		'name' => 'cache_key',
		'desc' => 'The key of the cache provider to use; leave empty to use the cache_resource_key cache partition (default is "resource").',
		'type' => 'textfield',
		'options' => '',
		'value' => false,
	)
	,array(
		'name' => 'cache_handler',
		'desc' => 'The cache provider implementation to use; leave empty unless you are caching to a custom cache_key.',
		'type' => 'textfield',
		'options' => '',
		'value' => false,
	)
	,array(
		'name' => 'cache_expires',
		'desc' => 'The number of seconds before the cached pages expire and must be regenerated; leave empty to use the cache provider option for cache_expires.',
		'type' => 'textfield',
		'options' => '',
		'value' => false,
	)
	,array(
		'name' => 'pageNavScheme',
		'desc' => 'Optionally specify a scheme for use when generating page navigation links; will use link_tag_scheme if empty or not specified (default is empty).',
		'type' => 'textfield',
		'options' => '',
		'value' => '',
	)
	,array(
		'name' => 'pageSkipTpl',
		'desc' => 'Content representing the skipped pages in the beginning or ending of pagination.',
		'type' => 'textfield',
		'options' => '',
		'value' => '<li class="control">...</li>',
	)
	,array(
		'name' => 'showEdgePages',
		'desc' => 'This parameter enable displaying first and last pages with skipped pages. Number of this pages are equal pageLimit. For example: pageLimit is 2, current page is 6 and total pages is 51 will display: 1 2 ... 5 6 7 ... 50 51This parameter enable displaying first, last and skipped pages. Number of this pages are equal to pageLimit. For example: pageLimit is 2, current page is 6 and total pages is 51 will display: "1 2 ... 5 6 7 ... 50 51"',
		'type' => 'combo-boolean',
		'options' => '',
		'value' => true,
	)
);

return $properties;