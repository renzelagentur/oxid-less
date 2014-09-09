<?php

class raless_oxutilsview extends raless_oxutilsview_parent
{
    /**
     * @param Smarty $oSmarty
     */
    protected function _fillCommonSmartyProperties($oSmarty)
    {
        parent::_fillCommonSmartyProperties($oSmarty);
        $aPluginsDir = $oSmarty->plugins_dir;
        $aPluginsDir[] = oxRegistry::getConfig()->getModulesDir() . 'ra/less/smarty/plugins';
        $oSmarty->plugins_dir = $aPluginsDir;
    }
}
