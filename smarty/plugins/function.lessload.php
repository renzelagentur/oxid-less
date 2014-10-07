<?php

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

        /* @var $oActiveTheme \oxTheme */
        $oActiveTheme = oxNew('oxTheme');
        $oActiveTheme->load($oActiveTheme->getActiveThemeId());
        $iShop = $myConfig->getShopId();

        do {
            $sLessFile = $myConfig->getDir($sStyle, 'src/less', $myConfig->isAdmin(), oxRegistry::getLang()->getBaseLanguage(), $iShop, $oActiveTheme->getId());
		    $oActiveTheme = $oActiveTheme->getParent();
        }
        while(!is_null($oActiveTheme) && !file_exists($sLessFile));

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
            }
            $sFilename = md5($sFilename) . '.css';

            $sGenDir = $myConfig->getOutDir() . 'gen/';
            if(!is_dir($sGenDir)) {
                mkdir($sGenDir);
            }

            $sCssFile = $sGenDir . $sFilename;
            $sCssFile = str_replace('.less', '.css', $sCssFile);
            $sCssUrl = str_replace($myConfig->getOutDir(), $myConfig->getOutUrl(), $sCssFile);

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

    $params['include'] = $sCssUrl;
    if ($params['blNotUseOxStyle']) {
        return '<link rel="stylesheet" type="text/css" href="'.$sCssUrl.'" />'.PHP_EOL;
    } else {
        return smarty_function_oxstyle($params, $smarty);
    }
}
