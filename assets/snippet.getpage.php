<?php
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
$properties['pageFirstTpl'] = empty($pageFirstTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">First</a></li>" : $pageFirstTpl;
$properties['pageLastTpl'] = empty($pageLastTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">Last</a></li>" : $pageLastTpl;
$properties['pagePrevTpl'] = empty($pagePrevTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">&lt;&lt;</a></li>" : $pagePrevTpl;
$properties['pageNextTpl'] = empty($pageNextTpl) ? "<li class=\"control\"><a[[+title]] href=\"[[+href]]\">&gt;&gt;</a></li>" : $pageNextTpl;


$elementObj = $modx->getObject($properties['elementClass'], array('name' => $properties['element']));
if ($elementObj) {
    $output = $elementObj->process($properties);
}

include_once($modx->getOption('core_path', $properties, MODX_CORE_PATH) . 'components/getpage/include.getpage.php');

$qs = array();

$totalSet = $modx->getPlaceholder($properties['totalVar']);
$properties['total'] = (($totalSet = intval($totalSet)) ? $totalSet : $properties['total']);
$properties['pageCount'] = ($properties['total'] && $properties['limit'] ? ceil($properties['total'] / $properties['limit']) : 1);
if (empty($properties['total']) || empty($properties['limit']) || $properties['total'] <= $properties['limit']) {
    $properties['page'] = 1;
} else {
    $properties['qs'] = array();
    $properties[$properties['pageNavVar']] = getpage_buildControls($modx, $properties);
    if ($properties['page'] > 1) {
        $qs[] = "{$properties['pageVarKey']}={$properties['page']}";
    }
}

$queryString = !empty($qs) ? implode('&', $qs) : '';
$properties['qs'] = $queryString;
$properties['pageUrl'] = $modx->makeUrl($modx->resource->get('id'), '', $queryString);

$modx->setPlaceholders($properties, $properties['namespace']);

return $output;