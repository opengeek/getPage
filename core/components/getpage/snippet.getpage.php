<?php
/**
 * @package getpage
 */
$output = '';

$properties =& $scriptProperties;
$properties['page'] = (isset($_GET[$properties['pageVarKey']]) && ($page = intval($_GET[$properties['pageVarKey']]))) ? $page : null;
if ($properties['page'] === null) {
    $properties['page'] = (isset($_REQUEST[$properties['pageVarKey']]) && ($page = intval($_REQUEST[$properties['pageVarKey']]))) ? $page : 1;
}
$properties['limit'] = (isset($_GET['limit'])) ? intval($_GET['limit']) : null;
if ($properties['limit'] === null) {
    $properties['limit'] = (isset($_REQUEST['limit'])) ? intval($_REQUEST['limit']) : intval($limit);
}
$properties['offset'] = (!empty($properties['limit']) && !empty($properties['page'])) ? ($properties['limit'] * ($properties['page'] - 1)) : 0;
$properties['totalVar'] = empty($totalVar) ? "total" : $totalVar;
$properties[$properties['totalVar']] = !empty($properties[$properties['totalVar']]) && $total = intval($properties[$properties['totalVar']]) ? $total : 0;
$properties['pageOneLimit'] = (!empty($pageOneLimit) && $pageOneLimit = intval($pageOneLimit)) ? $pageOneLimit : $properties['limit'];
$properties['actualLimit'] = $properties['limit'];
$properties['pageLimit'] = isset($pageLimit) && is_numeric($pageLimit) ? intval($pageLimit) : 5;
$properties['element'] = empty($element) ? '' : $element;
$properties['elementClass'] = empty($elementClass) ? 'modChunk' : $elementClass;
$properties['pageNavVar'] = empty($pageNavVar) ? 'page.nav' : $pageNavVar;
$properties['pageNavTpl'] = !isset($pageNavTpl) ? "<li[[+classes]]><a[[+classes]][[+title]] href=\"[[+href]]\">[[+pageNo]]</a></li>" : $pageNavTpl;
$properties['pageNavOuterTpl'] = !isset($pageNavOuterTpl) ? "[[+first]][[+prev]][[+pages]][[+next]][[+last]]" : $pageNavOuterTpl;
$properties['pageActiveTpl'] = !isset($pageActiveTpl) ? "<li[[+activeClasses:default=` class=\"active\"`]]><a[[+activeClasses:default=` class=\"active\"`]][[+title]] href=\"[[+href]]\">[[+pageNo]]</a></li>" : $pageActiveTpl;
$properties['pageFirstTpl'] = !isset($pageFirstTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">First</a></li>" : $pageFirstTpl;
$properties['pageLastTpl'] = !isset($pageLastTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">Last</a></li>" : $pageLastTpl;
$properties['pagePrevTpl'] = !isset($pagePrevTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">&lt;&lt;</a></li>" : $pagePrevTpl;
$properties['pageNextTpl'] = !isset($pageNextTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">&gt;&gt;</a></li>" : $pageNextTpl;
$properties['toPlaceholder'] = !empty($toPlaceholder) ? $toPlaceholder : '';
$properties['cache'] = isset($cache) ? (boolean) $cache : (boolean) $modx->getOption('cache_resource', null, false);
if (empty($cache_key)) $properties[xPDO::OPT_CACHE_KEY] = $modx->getOption('cache_resource_key', null, 'resource');
if (empty($cache_handler)) $properties[xPDO::OPT_CACHE_HANDLER] = $modx->getOption('cache_resource_handler', null, 'xPDOFileCache');
if (empty($cache_expires)) $properties[xPDO::OPT_CACHE_EXPIRES] = (integer) $modx->getOption('cache_resource_expires', null, 0);

if ($properties['page'] == 1 && $properties['pageOneLimit'] !== $properties['actualLimit']) {
    $properties['limit'] = $properties['pageOneLimit'];
}

if ($properties['cache']) {
    $properties['cachePageKey'] = $modx->resource->getCacheKey() . '/' . $properties['page'] . '/' . md5(http_build_query($modx->request->getParameters()) . http_build_query($scriptProperties));
    $properties['cacheOptions'] = array(
        xPDO::OPT_CACHE_KEY => $properties[xPDO::OPT_CACHE_KEY],
        xPDO::OPT_CACHE_HANDLER => $properties[xPDO::OPT_CACHE_HANDLER],
        xPDO::OPT_CACHE_EXPIRES => $properties[xPDO::OPT_CACHE_EXPIRES],
    );
}
$cached = false;
if ($properties['cache']) {
    if ($modx->getCacheManager()) {
        $cached = $modx->cacheManager->get($properties['cachePageKey'], $properties['cacheOptions']);
    }
}
if (empty($cached) || !isset($cached['properties']) || !isset($cached['output'])) {
    $elementObj = $modx->getObject($properties['elementClass'], array('name' => $properties['element']));
    if ($elementObj) {
        $elementObj->setCacheable(false);
        if (!empty($properties['toPlaceholder'])) {
            $elementObj->process($properties);
            $output = $modx->getPlaceholder($properties['toPlaceholder']);
        } else {
            $output = $elementObj->process($properties);
        }
    }

    include_once $modx->getOption('getpage.core_path',$properties,$modx->getOption('core_path', $properties, MODX_CORE_PATH) . 'components/getpage/').'include.getpage.php';

    $qs = $modx->request->getParameters();
    $properties['qs'] =& $qs;

    $totalSet = $modx->getPlaceholder($properties['totalVar']);
    $properties[$properties['totalVar']] = (($totalSet = intval($totalSet)) ? $totalSet : $properties[$properties['totalVar']]);
    if (!empty($properties[$properties['totalVar']]) && !empty($properties['actualLimit'])) {
        if ($properties['pageOneLimit'] !== $properties['actualLimit']) {
            $adjustedTotal = $properties[$properties['totalVar']] - $properties['pageOneLimit'];
            $properties['pageCount'] = $adjustedTotal > 0 ? ceil($adjustedTotal / $properties['actualLimit']) + 1 : 1;
        } else {
            $properties['pageCount'] = ceil($properties[$properties['totalVar']] / $properties['actualLimit']);
        }
    } else {
        $properties['pageCount'] = 1;
    }
    if (empty($properties[$properties['totalVar']]) || empty($properties['actualLimit']) || $properties[$properties['totalVar']] <= $properties['actualLimit'] || ($properties['page'] == 1 && $properties[$properties['totalVar']] <= $properties['pageOneLimit'])) {
        $properties['page'] = 1;
    } else {
        $pageNav = getpage_buildControls($modx, $properties);
        $properties[$properties['pageNavVar']] = $modx->newObject('modChunk')->process(array_merge($properties, $pageNav), $properties['pageNavOuterTpl']);
        if ($properties['page'] > 1) {
            $qs[$properties['pageVarKey']] = $properties['page'];
        }
    }

    $properties['firstItem'] = $properties['offset'] + 1;
    $properties['lastItem'] = ($properties['offset'] + $properties['limit']) < $totalSet ? ($properties['offset'] + $properties['limit']) : $totalSet;

    $properties['pageUrl'] = $modx->makeUrl($modx->resource->get('id'), '', $qs);
    if ($properties['cache'] && $modx->getCacheManager()) {
        $cached = array('properties' => $properties, 'output' => $output);
        $modx->cacheManager->set($properties['cachePageKey'], $cached, $properties[xPDO::OPT_CACHE_EXPIRES], $properties['cacheOptions']);
    }
} else {
    $properties = $cached['properties'];
    $output = $cached['output'];
}
$modx->setPlaceholders($properties, $properties['namespace']);
if (!empty($properties['toPlaceholder'])) {
    $modx->setPlaceholder($properties['toPlaceholder'], $output);
    $output = '';
}

return $output;
