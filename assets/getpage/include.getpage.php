<?php
function getpage_buildControls(& $modx, $properties) {
    $nav = array();
    $qs = !empty($properties['qs']) ? $properties['qs'] : array();
    $page = !empty($properties['page']) ? $properties['page'] : 1;
    $pageCount = !empty($properties['pageCount']) ? $properties['pageCount'] : 1;
    $pageLimit = $properties['pageLimit'];
    extract($properties, EXTR_SKIP);
    if ($pageCount > 1 && !empty($pageNavTpl)) {
        for ($i = 1; $i <= $pageCount; $i++) {
            if ($i == 1 && $i != $page && !empty($pageFirstTpl)) {
                $nav['first'] = getpage_makeUrl($modx, $properties, $i, $pageFirstTpl);
                if (!empty($pagePrevTpl) && ($page - 1) >= 1) {
                    $nav['prev'] = getpage_makeUrl($modx, $properties, $page - 1, $pagePrevTpl);
                }
            }
            if ($i >= $page - $pageLimit && $i <= $page + $pageLimit) {
                $nav[$i] = getpage_makeUrl($modx, $properties, $i, $pageNavTpl);
            }
            if ($i == $pageCount && $i != $page && !empty($pageLastTpl)) {
                if (!empty($pageNextTpl) && ($page + 1) <= $pageCount) {
                    $nav['next'] = getpage_makeUrl($modx, $properties, $page + 1, $pageNextTpl);
                }
                $nav['last'] = getpage_makeUrl($modx, $properties, $i, $pageLastTpl);
            }
        }
    }
    return implode("\n", $nav);
}

function getpage_makeUrl(& $modx, $properties, $pageNo, $tpl) {
    $qs = implode('&', $properties['qs']);
    if (!empty ($qs)) $qs.= '&';
    $properties['href'] = $modx->makeUrl($modx->resource->get('id'), '', $qs . "{$properties['pageVarKey']}={$pageNo}");
    $properties['pageNo'] = $pageNo;
    $nav= $modx->newObject('modChunk')->process($properties, $tpl);
    return $nav;
}
