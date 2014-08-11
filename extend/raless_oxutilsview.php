<?php

class raless_oxutilsview extends raless_oxutilsview_parent
{
    
    protected function _fillCommonSmartyProperties($oSmarty)
    {
        parent::_fillCommonSmartyProperties($oSmarty);

        $cfg = oxRegistry::getConfig();

        $aPluginsDir = $oSmarty->plugins_dir;
        $aPluginsDir[] = $cfg->getModulesDir() . 'ra/less/smarty/plugins';
        

        $oSmarty->plugins_dir = $aPluginsDir;
    }
}
