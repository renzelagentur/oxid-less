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
            $sPath = getModuleIdByFile($sStyle);
        }

        /* @var $oActiveTheme \oxTheme */
        $oActiveTheme = oxNew('oxTheme');
        $oActiveTheme->load($oActiveTheme->getActiveThemeId());
        $iShop = $myConfig->getShopId();

        // less file not in a module path
        if (!$blIsModule) {
            do {
                $sLessPathNFile = $myConfig->getDir($sLessFile, 'src/less', $myConfig->isAdmin(), oxRegistry::getLang()->getBaseLanguage(), $iShop, $oActiveTheme->getId());
                $oLastActiveTheme = $oActiveTheme;
                $oActiveTheme = $oActiveTheme->getParent();
            } while (!is_null($oActiveTheme) && !file_exists($sLessPathNFile));

            $sLessFile = $sLessPathNFile;
            $sPath = '/' . $myConfig->getOutDir(false) . $oLastActiveTheme->getActiveThemeId() . '/src/';
        }

        // File not found ?
        if (!$sLessFile) {
            if ($myConfig->getConfigParam('iDebug') != 0) {
                $sError = "{lessload} resource not found: " . htmlspecialchars($params['include']);
                trigger_error($sError, E_USER_WARNING);
            }

            return;
        } else {
            $sCssUrl = compile($sPath, $sLessFile);
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
 * get module by file
 *
 * @param string $file module file path
 *
 * @return string
 */
function getModuleIdByFile($file)
{
    $oModule = oxNew('oxModule');
    $sModuleUrl = str_replace(oxRegistry::getConfig()->getShopUrl() . 'modules/', '', $file);
    $sExplodedModulePath = explode('/', $sModuleUrl);
    if (!$oModule->loadByDir($sExplodedModulePath[0] . '/' . $sExplodedModulePath[1])) {
        $oModule->loadByDir($sExplodedModulePath[0]);
    }
    return oxRegistry::getConfig()->getShopUrl() . 'modules/' . $oModule->getModulePath() . '/';
}

/**
 * compile less file
 *
 * @param string $sPath     path to sources
 * @param string $sLessFile lessFilePath
 *
 * @return mixed|null
 */
function compile($sPath, $sLessFile)
{
    $myConfig = oxRegistry::getConfig();

    $sGenDir = $myConfig->getOutDir() . 'gen/';
    if (!is_dir($sGenDir)) {
        mkdir($sGenDir);
    }

    try {
        $options = array(
            'compress'     => true,
            'cache_method' => 'serialize',
            'cache_dir'    => oxRegistry::get("oxConfigFile")->getVar("sCompileDir") . 'less'
        );

        $variables = array();
        foreach (explode(',', trim($myConfig->getShopConfVar('sVariables', null, 'module:raless'))) as $sVar) {
            $sThemeConfVar = getThemeConfigVar($sVar);
            if (!is_null($sThemeConfVar) && $sThemeConfVar !== '') {
                $variables[$sVar] = $sThemeConfVar;
            }
        }

        $sCssFile = Less_Cache::Get(array($sLessFile => $sPath), $options, $variables);
        if (!file_exists($sGenDir . $sCssFile)) {
            copy(oxRegistry::get("oxConfigFile")->getVar("sCompileDir") . 'less/' . $sCssFile, $sGenDir . $sCssFile);
        }

        return '/out/gen/' . $sCssFile;
    } catch (Exception $e) {
        if ($myConfig->getConfigParam('iDebug') != 0) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }
    }

    return null;
}
