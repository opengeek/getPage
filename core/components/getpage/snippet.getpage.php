<?php
/**
 * @package getpage
 */
$output = '';

$properties =& $scriptProperties;
$properties['page'] = (isset($_REQUEST[$properties['pageVarKey']]) && ($page = intval($_REQUEST[$properties['pageVarKey']]))) ? $page : 1;
$properties['limit'] = (!empty($_REQUEST['limit']) && ($limit = intval($_REQUEST['limit']))) ? $limit : intval($limit);
$properties['offset'] = (!empty($properties['limit']) && !empty($properties['page'])) ? ($properties['limit'] * ($properties['page'] - 1)) : 0;
$properties['totalVar'] = empty($totalVar) ? "total" : $totalVar;
$properties['total'] = !empty($properties['total']) && $total = intval($properties['total']) ? $total : 0;
$properties['pageLimit'] = !empty($pageLimit) && ($pageLimit = intval($pageLimit)) ? $pageLimit : 5;
$properties['element'] = empty($element) ? '' : $element;
$properties['elementClass'] = empty($elementClass) ? 'modChunk' : $elementClass;
$properties['pageNavVar'] = empty($pageNavVar) ? 'page.nav' : $pageNavVar;
$properties['pageNavTpl'] = empty($pageNavTpl) ? "<li[[+classes]]><a[[+classes]][[+title]] href=\"[[+href]]\">[[+pageNo]]</a></li>" : $pageNavTpl;
$properties['pageActiveTpl'] = empty($pageActiveTpl) ? "<li[[+activeClasses:default=` class=\"active\"`]]><a[[+activeClasses:default=` class=\"active\"`]][[+title]] href=\"[[+href]]\">[[+pageNo]]</a></li>" : $pageActiveTpl;
$properties['pageFirstTpl'] = empty($pageFirstTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">First</a></li>" : $pageFirstTpl;
$properties['pageLastTpl'] = empty($pageLastTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">Last</a></li>" : $pageLastTpl;
$properties['pagePrevTpl'] = empty($pagePrevTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">&lt;&lt;</a></li>" : $pagePrevTpl;
$properties['pageNextTpl'] = empty($pageNextTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">&gt;&gt;</a></li>" : $pageNextTpl;
$properties['toPlaceholder'] = !empty($toPlaceholder) ? $toPlaceholder : '';
$properties['cache'] = isset($cache) ? (boolean) $cache : (boolean) $modx->getOption('cache_resource', $properties, false);
$properties[xPDO::OPT_CACHE_KEY] = $modx->getOption('cache_resource_key', $properties, 'default');
$properties[xPDO::OPT_CACHE_HANDLER] = $modx->getOption('cache_resource_handler', $properties, 'xPDOFileCache');
$properties[xPDO::OPT_CACHE_EXPIRES] = (integer) $modx->getOption(xPDO::OPT_CACHE_EXPIRES, $properties, 0);

if ($properties['cache']) {
    $properties['cachePageKey'] = $modx->resource->getCacheKey() . '/' . $properties['page'] . '/' . md5(implode('', $modx->request->getParameters()));
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

    include_once $modx->getOption('core_path', $properties, MODX_CORE_PATH) . 'components/getpage/include.getpage.php';

    $qs = $modx->request->getParameters();
    $properties['qs'] =& $qs;

    $totalSet = $modx->getPlaceholder($properties['totalVar']);
    $properties['total'] = (($totalSet = intval($totalSet)) ? $totalSet : $properties['total']);
    $properties['pageCount'] = ($properties['total'] && $properties['limit'] ? ceil($properties['total'] / $properties['limit']) : 1);
    if (empty($properties['total']) || empty($properties['limit']) || $properties['total'] <= $properties['limit']) {
        $properties['page'] = 1;
    } else {
        $properties[$properties['pageNavVar']] = getpage_buildControls($modx, $properties);
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