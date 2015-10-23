<?php
/**
 * ralessgeneratorservice.php
 *
 * @version   GIT: $Id$ PHP5.4 (16.10.2014)
 * @author    Robin Lehrmann <info@renzel-agentur.de>
 * @copyright Copyright (C) 22.10.2014 renzel.agentur GmbH. All rights reserved.
 * @license   http://www.renzel-agentur.de/licenses/raoxid-1.0.txt
 * @link      http://www.renzel-agentur.de/
 *
 */
class RALessGeneratorService
{
    const GEN_DIR = 'out/gen/';

    /**
     * @var array
     */
    private $_fileCache = array();

    /**
     * creates gen dir
     */
    public function __construct()
    {
        $sGenDir = OX_BASE_PATH . self::GEN_DIR;
        if (!is_dir($sGenDir)) {
            mkdir($sGenDir);
        }
    }

    /**
     * get less files from template
     *
     * @param string $tpl template to parse
     *
     * @return array
     */
    private function _parseForLess($tpl)
    {
        $content = file_get_contents($tpl);
        if ($pos = strpos($content, 'lessload')) {
            preg_match_all('/\'*([a-zA-Z0-9\/\_\-]+.less)/', $content, $rawResults);
            if (is_array($rawResults)) {
                $results = array();
                foreach ($rawResults as $result) {
                    $results[] = $result[0];
                }
                return array_unique($results);
            }
        }
        return array();
    }

    /**
     * get files by extension
     *
     * @param string $extension extension e.g. tpl,less,css,js
     *
     * @return array
     */
    private function _getFilesByExtension($extension)
    {
        if (!isset($this->_fileCache[$extension])) {
            $this->_fileCache[$extension] = array();
            /** @var SplFileInfo $file */
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(OX_BASE_PATH)) as $file) {
                if ($file->isFile() && $file->getExtension() === $extension) {
                    $this->_fileCache[$extension][] = $file->getRealPath();
                }
            }
        }
        return $this->_fileCache[$extension];
    }

    /**
     * get url
     *
     * @return string
     */
    private function _getUrl()
    {
        $cdnUrl = oxRegistry::getConfig()->getShopConfVar('sCDNUrl', 1, 'module:raless');
        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/';
        }
        return '/';
    }

    /**
     * get module url by less file
     *
     * @param string $file less file of module
     *
     * @return string
     */
    private function _getModuleUrlByFile($file)
    {
        $oModule = oxNew('oxModule');
        $sModulePath = str_replace(OX_BASE_PATH . 'modules/', '', $file);
        $aExplodedModulePath = explode('/', $sModulePath);
        if (!$oModule->loadByDir($aExplodedModulePath[0] . '/' . $aExplodedModulePath[1])) {
            if (!$oModule->loadByDir($aExplodedModulePath[0])) {
                return false;
            }
        }
        return $this->_getUrl() . 'modules/' . $oModule->getModulePath() . '/';
    }

    /**
     * get used less files
     *
     * @return array
     */
    private function _getUsedLessFiles()
    {
        $lessFiles = array();
        foreach ($this->_getFilesByExtension('tpl') as $file) {
            foreach ($this->_parseForLess($file) as $lessFile) {
                foreach ($this->_getFilesByExtension('less') as $less) {
                    if (basename($less) === basename($lessFile)) {
                        $lessFiles[] = $less;
                    }
                }
            }
        }
        return array_unique($lessFiles);
    }

    /**
     * generate css files
     *
     * @param array $lessFiles less file(s) to compile
     *
     * @return array
     *
     * @throws Exception
     */
    public function generate(array $lessFiles = array())
    {
        $sGenDir = OX_BASE_PATH . self::GEN_DIR;
        $options = array(
            'compress' => true,
            'cache_method' => 'serialize',
            'cache_dir' => '/' . trim(oxRegistry::get("oxConfigFile")->getVar("sCompileDir"), '/') . '/less'
        );
        if (empty($lessFiles)) {
            $lessFiles = $this->_getUsedLessFiles();
        }
        $results = array();
        foreach ($lessFiles as $less) {
            $hash = substr(md5(realpath($less)), 0, 5);
            $sCssFile = $hash . '_' . str_replace('.less', '', basename($less)) . '.css';
            if (strpos($less, 'module')) {
                $path = $this->_getModuleUrlByFile($less);
            } else {
                $path = $this->_getUrl();
            }
            copy(oxRegistry::get("oxConfigFile")->getVar("sCompileDir") . 'less/' . Less_Cache::Get(array($less => $path), $options), $sGenDir . $sCssFile);
            $results[$less] = self::GEN_DIR . $sCssFile;
        }
        return $results;
    }
}
