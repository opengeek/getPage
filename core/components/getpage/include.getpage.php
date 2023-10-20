<?php
/**
 * @package getpage
 */
function getpage_buildControls(& $modx, $properties) {
    $nav = array(
        'pages' => array()
    );
    $qs = !empty($properties['qs']) ? $properties['qs'] : array();
    $page = !empty($properties['page']) ? $properties['page'] : 1;
    $pageCount = !empty($properties['pageCount']) ? $properties['pageCount'] : 1;
    $pageLimit = $properties['pageLimit'];
    extract($properties, EXTR_SKIP);
    if ($pageCount > 1 && !empty($pageNavTpl)) {
        for ($i = 1; $i <= $pageCount; $i++) {
            if ($i == 1 && $i != $page && !empty($pageFirstTpl)) {
                $nav['first'] = getpage_makeUrl($modx, $properties, $i, $pageFirstTpl, 'pageFirstTpl');
                if (!empty($pagePrevTpl) && ($page - 1) >= 1) {
                    $nav['prev'] = getpage_makeUrl($modx, $properties, $page - 1, $pagePrevTpl, 'pagePrevTpl');
                }
            }
            if (empty($pageLimit) || ($i >= $page - $pageLimit && $i <= $page + $pageLimit)) {
                if ($i == $page) {
                    $nav['pages'][$i] = getpage_makeUrl($modx, $properties, $i, $pageActiveTpl, 'pageActiveTpl');
                } else {
                    $nav['pages'][$i] = getpage_makeUrl($modx, $properties, $i, $pageNavTpl, 'pageNavTpl');
                }
            }
            if ($i == $pageCount && $i != $page && !empty($pageLastTpl)) {
                if (!empty($pageNextTpl) && ($page + 1) <= $pageCount) {
                    $nav['next'] = getpage_makeUrl($modx, $properties, $page + 1, $pageNextTpl, 'pageNextTpl');
                }
                $nav['last'] = getpage_makeUrl($modx, $properties, $i, $pageLastTpl, 'pageLastTpl');
            }
        }
        $nav['pages'] = implode("\n", $nav['pages']);
    }
    return $nav;
}

function getpage_makeUrl(& $modx, $properties, $pageNo, $tpl, $tplName) {
    $qs = $properties['qs'];
    $scheme = !empty($properties['pageNavScheme']) ? $properties['pageNavScheme'] : $modx->getOption('link_tag_scheme', $properties, -1);
    
    if ($properties['pageNavScheme'] === 'path') {
        
        unset($qs[$properties['pageVarKey']]);
        $properties['href'] = $modx->makeUrl($modx->resource->get('id'), '', '', $modx->getOption('link_tag_scheme', $properties, -1));
        if ($pageNo !== 1) {
            $properties['href'] = rtrim($properties['href'], '/').$properties['pathUrlSeparator'];
            if (!$properties['pathHidePageVarKey']) $properties['href'] .= $properties['pageVarKey'].$properties['pathNumberSeparator'];
            $properties['href'] .= $pageNo;
        }
        if (!empty($qs)) $properties['href'] .= '?'. http_build_query($qs);
        
    } else {
        
        if ($pageNo === 1) {
            unset($qs[$properties['pageVarKey']]);
        } else {
            $qs[$properties['pageVarKey']] = $pageNo;
        }
        $properties['href'] = $modx->makeUrl($modx->resource->get('id'), '', $qs, $scheme);
        
    }
    
    $properties['pageNo'] = $pageNo;
    $chunk = $modx->newObject('modChunk');
    $chunk->set('name', $tplName);
    $nav = $chunk->process($properties, $tpl);
    return $nav;
}
