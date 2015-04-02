<?php
/**
 * function.lessload.php
 *
 * @version   GIT: $Id$ PHP5.4 (16.10.2014)
 * @author    Robin Lehrmann <info@renzel-agentur.de>
 * @copyright Copyright (C) 22.10.2014 renzel.agentur GmbH. All rights reserved.
 * @license   MIT
 * @link      http://www.renzel-agentur.de/
 */

require_once OX_BASE_PATH . '/core/smarty/plugins/function.oxstyle.php';

/**
 * less load smarty plugin
 *
 * @param array $params params
 * @param mixed $smarty Smarty object
 *
 * @return string
 */
function smarty_function_lessload($params, $smarty)
{
    $myConfig = oxRegistry::getConfig();
    $sShopUrl = oxRegistry::getConfig()->getCurrentShopUrl();
    $blIsModule = false;

    if ($params['include']) {
        $sStyle = $params['include'];
        $sLessFile = $sStyle;

        if (preg_match('#^http?://#', $sStyle)) {
            $sLessFile = str_replace($sShopUrl, OX_BASE_PATH, $sLessFile);
            $blIsModule = true;
        }

        /* @var $oActiveTheme \oxTheme */
        $oActiveTheme = oxNew('oxTheme');
        $oActiveTheme->load($oActiveTheme->getActiveThemeId());
        $iShop = $myConfig->getShopId();

        // less file not in a module path
        if (!$blIsModule) {
            do {
                $sLessPathNFile = $myConfig->getDir($sLessFile, 'src/less', $myConfig->isAdmin(), oxRegistry::getLang()->getBaseLanguage(), $iShop, $oActiveTheme->getId());
                $oActiveTheme = $oActiveTheme->getParent();
            } while (!is_null($oActiveTheme) && !file_exists($sLessPathNFile));

            $sLessFile = $sLessPathNFile;
        }

        // File not found ?
        if (!$sLessFile) {
            if ($myConfig->getConfigParam('iDebug') != 0) {
                $sError = "{lessload} resource not found: " . htmlspecialchars($params['include']);
                trigger_error($sError, E_USER_WARNING);
            }

            return;
        } else {
            $sCssUrl = compile($sShopUrl, $sLessFile, $myConfig);
        }
    }

    $params['include'] = $sCssUrl;
    if ($params['blNotUseOxStyle']) {
        return '<link rel="stylesheet" type="text/css" href="' . $sCssUrl . '" />' . PHP_EOL;
    } else {
        return smarty_function_oxstyle($params, $smarty);
    }
}

/**
 * get config param of active theme
 *
 * @param string $sKey var name
 *
 * @return mixed
 */
function getThemeConfigVar($sKey)
{
    /** @var \oxTheme $oTheme */
    $oTheme = oxNew('oxTheme');

    return oxRegistry::getConfig()->getShopConfVar($sKey, null, 'theme:' . $oTheme->getActiveThemeId());
}


/**
 * compile less file
 *
 * @param string $sShopUrl  shopurl
 * @param string $sLessFile lessfilepath
 * @param object $myConfig  shopconfig
 *
 * @return mixed|null
 */
function compile($sShopUrl, $sLessFile, $myConfig)
{

    $sFilename = str_replace('/', '_', $sLessFile);
    $sFilename = md5($sFilename . $sShopUrl) . '.css';

    $sGenDir = $myConfig->getOutDir() . 'gen/';
    if (!is_dir($sGenDir)) {
        mkdir($sGenDir);
    }

    /** @var \oxTheme $oTheme */
    $oTheme = oxNew('oxTheme');

    try {
        $options = array(
            'compress'     => true,
            'cache_method' => 'serialize',
            'cache_dir'    => oxRegistry::get("oxConfigFile")->getVar("sCompileDir") . 'less'
        );

        $variables = array();
        foreach (explode(',', trim($myConfig->getShopConfVar('sVariables', null, 'module:raless'))) as $sVar) {
            if (!is_null(getThemeConfigVar($sVar)) && getThemeConfigVar($sVar) !== '') {
                $variables[$sVar] = getThemeConfigVar($sVar);
            }
        }

        $sCssFile = Less_Cache::Get(array($sLessFile =>  $sShopUrl . $myConfig->getOutDir(false) . $oTheme->getActiveThemeId() . '/src/'), $options, $variables);
        if (!file_exists($sGenDir . $sCssFile)) {
            copy(oxRegistry::get("oxConfigFile")->getVar("sCompileDir") . 'less/' . $sCssFile, $sGenDir . $sCssFile);
        }

        return $myConfig->getCurrentShopUrl() . 'out/gen/' . $sCssFile;
    } catch (Exception $e) {
        if ($myConfig->getConfigParam('iDebug') != 0) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }
    }

    return null;
}
