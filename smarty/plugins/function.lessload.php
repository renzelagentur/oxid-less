<?php

include_once OX_BASE_PATH . 'core/smarty/plugins/function.oxstyle.php';

function smarty_function_lessload($params, &$smarty) {

    $myConfig = oxRegistry::getConfig();
    
    $sShopUrl = oxRegistry::getConfig()->getShopUrl();
    
    if ($params['include']) {
        $sStyle = $params['include'];
        $sLessFile = $sStyle;
        
        if (!preg_match('#^http?://#', $sStyle)) {
            $aStyle = explode('?', $sStyle);
            $sResourceDir = $myConfig->getResourceDir($myConfig->isAdmin());

            $sLessFile = str_replace($sShopUrl, OX_BASE_PATH, $sLessFile);
        }
        
        // File not found ?
        if (!$sLessFile) {
            if ($myConfig->getConfigParam('iDebug') != 0) {
                $sError = "{lessload} resource not found: " . htmlspecialchars($params['include']);
                trigger_error($sError, E_USER_WARNING);
            }
            return;
        } else {
            
            $less = new lessc;
            $less->setPreserveComments(false);
            
            $sFilename = str_replace('/', '_', str_replace($sShopUrl, '', $sLessFile));
            
            if ($myConfig->isProductiveMode()) {
                $less->setFormatter("compressed");
                $sFilename = md5($sFilename);
            }
            
            $sGenDir = $myConfig->getResourceDir($myConfig->isAdmin()) . 'gen/';
            $sGenDir = str_replace('src/gen', 'gen', $sGenDir);
            
            if(!file_exists($sGenDir)) {
                mkdir($sGenDir);
            }
            
            $sCssFile = $sGenDir . $sFilename;
            $sCssFile = str_replace('.less', '.css', $sCssFile);
            
            try {
                // @todo: use cachedCompile instead
                $less->checkedCompile($sLessFile, $sCssFile);
            } catch (Exception $e) {
                if ($myConfig->getConfigParam('iDebug') != 0) {
                    trigger_error($e->getMessage(), E_USER_WARNING);
                }
            }
        }
    }
    
    $params['include'] = str_replace($sShopUrl, OX_BASE_PATH, $sCssFile);
    
    return smarty_function_oxstyle($params, &$smarty);
}
