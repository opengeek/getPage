<?php

/* @var modX $modx */
function gpe_buildControls(& $modx, $properties) {
	$nav = array();
	$qs = !empty($properties['qs']) ? $properties['qs'] : array();
	$page = !empty($properties['page']) ? $properties['page'] : 1;
	$pageCount = !empty($properties['pageCount']) ? $properties['pageCount'] : 1;
	$pageLimit = $properties['pageLimit'];
	extract($properties, EXTR_SKIP);
	$first_pages = $last_pages = 0;
	if ($pageCount > 1 && !empty($pageNavTpl)) {
		for ($i = 1; $i <= $pageCount; $i++) {
			if ($i == 1 && $i != $page && !empty($pageFirstTpl)) {
				$nav['first'] = gpe_makeUrl($modx, $properties, $i, $pageFirstTpl);
				if (!empty($pagePrevTpl) && ($page - 1) >= 1) {
					$nav['prev'] = gpe_makeUrl($modx, $properties, $page - 1, $pagePrevTpl);
				}
			}

			if (empty($pageLimit) || ($i >= $page - $pageLimit && $i <= $page + $pageLimit)) {
				if (!array_key_exists('pages', $nav)) $nav['pages'] = array();
				if ($i == $page) {
					$nav['pages'][$i] = gpe_makeUrl($modx, $properties, $i, $pageActiveTpl);
				} else {
					$nav['pages'][$i] = gpe_makeUrl($modx, $properties, $i, $pageNavTpl);
				}
			}
			if (!empty($showEdgePages) && (!isset($nav['pages']) || !array_key_exists($i, $nav['pages']))) {
				if ($i < $page && $first_pages < $pageLimit) {
					$nav['pages'][$i] = gpe_makeUrl($modx, $properties, $i, $pageNavTpl);
					$first_pages++;
				}
				else if ($i > $pageCount - $pageLimit && $last_pages < $pageLimit) {
					$nav['pages'][$i] = gpe_makeUrl($modx, $properties, $i, $pageNavTpl);
					$last_pages++;
				}
			}

			if ($i == $pageCount && $i != $page && !empty($pageLastTpl)) {
				if (!empty($pageNextTpl) && ($page + 1) <= $pageCount) {
					$nav['next'] = gpe_makeUrl($modx, $properties, $page + 1, $pageNextTpl);
				}
				$nav['last'] = gpe_makeUrl($modx, $properties, $i, $pageLastTpl);
			}
		}
		if (!empty($showEdgePages)) {
			if ($first_pages == $pageLimit && !empty($pageSkipTpl)) {$nav['pages'][$page - $pageLimit] = $pageSkipTpl;}
			if ($last_pages == $pageLimit && !empty($pageSkipTpl)) {$nav['pages'][$page + $pageLimit] = $pageSkipTpl;}
		}
		$nav['pages'] = implode("\n", $nav['pages']);
	}
	return $nav;
}

/* @var modX $modx */
function gpe_makeUrl(& $modx, $properties, $pageNo, $tpl) {
	$qs = $properties['qs'];
	if ($pageNo === 1) {
		unset($qs[$properties['pageVarKey']]);
	} else {
		$qs[$properties['pageVarKey']] = $pageNo;
	}
	$scheme = !empty($properties['pageNavScheme']) ? $properties['pageNavScheme'] : $modx->getOption('link_tag_scheme', $properties, -1);

	if ($scheme == 'request' && $modx->getOption('friendly_urls')) {
			$q = $modx->getOption('request_param_alias',null,'q');
			$url = explode('&',$_SERVER['QUERY_STRING']);
			$href = str_replace("$q=",'/', $url[0]);
			$params = '';
			foreach ($qs as $k => $v) {
				$params .= "&$k=$v";
			}
			$properties['href'] = !empty($params) ? $href . '?' . substr($params,1) : $href;
	}
	else {
		$properties['href'] = $modx->makeUrl($modx->resource->get('id'), '', $qs, $scheme);
	}

	$properties['pageNo'] = $pageNo;
	$nav= $modx->newObject('modChunk')->process($properties, $tpl);
	return $nav;
}


function redirectToFirst(modX $modx, $properties) {
	unset(
		$_GET[$properties['pageVarKey']]
		,$_GET[$modx->getOption('request_param_alias',null,'q')]
	);
	$url = $modx->makeUrl($modx->resource->id, $modx->resource->context_key, $_GET, 'full');

	$modx->sendRedirect($url);
}