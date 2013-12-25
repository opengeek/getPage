<?php
/**
 * @package getpage
 */
function getpage_buildControls(& $modx, $properties) {
    $nav = array();
    $qs = !empty($properties['qs']) ? $properties['qs'] : array();
    $page = !empty($properties['page']) ? $properties['page'] : 1;
    $pageCount = !empty($properties['pageCount']) ? $properties['pageCount'] : 1;
    $pageLimit = $properties['pageLimit'];
    $placeholderNav = $properties['placeholderNav'];
    extract($properties, EXTR_SKIP);
    if ($pageCount > 1 && !empty($pageNavTpl)) {
        for ($i = 1; $i <= $pageCount; $i++) {
            if ($i == 1 && ($placeholderNav || $i != $page) && !empty($pageFirstTpl)) {
                $nav['first'] = getpage_makeUrl($modx, $properties, ($i == $page)? -1:$i, $pageFirstTpl);
                if (!empty($pagePrevTpl) && ($placeholderNav || $i != $page)) {
                    $nav['prev'] = getpage_makeUrl($modx, $properties, ($i == $page)? -1:$page - 1, $pagePrevTpl);
                }
            }
            if (empty($pageLimit) || ($i >= $page - $pageLimit && $i <= $page + $pageLimit)) {
                if (!array_key_exists('pages', $nav)) $nav['pages'] = array();
                if ($i == $page) {
                    $nav['pages'][$i] = getpage_makeUrl($modx, $properties, $i, $pageActiveTpl);
                } else {
                    $nav['pages'][$i] = getpage_makeUrl($modx, $properties, $i, $pageNavTpl);
                }
            }
            if ($i == $pageCount && ($placeholderNav || $i != $page) && !empty($pageLastTpl)) {
                if (!empty($pageNextTpl) && ($placeholderNav || ($page + 1) <= $pageCount)) {
                    $nav['next'] = getpage_makeUrl($modx, $properties, ($page == $pageCount)? -1:$page +1, $pageNextTpl);
                }
                $nav['last'] = getpage_makeUrl($modx, $properties, ($page == $pageCount)? -1:$i, $pageLastTpl);
            }
        }
        $nav['pages'] = implode("\n", $nav['pages']);
    }
    return $nav;
}

function getpage_makeUrl(& $modx, $properties, $pageNo, $tpl) {
    $qs = $properties['qs'];
    if ($pageNo === 1) {
        unset($qs[$properties['pageVarKey']]);
    } else {
        $qs[$properties['pageVarKey']] = $pageNo;
    }
    $scheme = !empty($properties['pageNavScheme']) ? $properties['pageNavScheme'] : $modx->getOption('link_tag_scheme', $properties, -1);
    $properties['href'] = ($pageNo!=-1)?$modx->makeUrl($modx->resource->get('id'), '', $qs, $scheme):'#';
    $properties['pageNo'] = $pageNo;

    if($pageNo == -1) //nav is placeholder, probebly a better way than -1 to pas this though
        $properties['classes'] = ' class="'.$properties['placeholderClasses'].'"';
    $nav= $modx->newObject('modChunk')->process($properties, $tpl);
    return $nav;
}
