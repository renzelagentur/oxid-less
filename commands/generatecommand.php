<?php
/**
 * generatecommand.php
 *
 * @version   GIT: $Id$ PHP5.4 (16.10.2014)
 * @author    Robin Lehrmann <info@renzel-agentur.de>
 * @copyright Copyright (C) 22.10.2014 renzel.agentur GmbH. All rights reserved.
 * @license   http://www.renzel-agentur.de/licenses/raoxid-1.0.txt
 * @link      http://www.renzel-agentur.de/
 *
 */
class GenerateCommand extends oxConsoleCommand
{
    /**
     * configure
     */
    public function configure()
    {
        $this->setName('less:generate');
        $this->setDescription('Compiles css');
    }

    /**
     * help
     *
     * @param oxIOutput $oOutput output
     */
    public function help(oxIOutput $oOutput)
    {
        $oOutput->writeLn('usage: less:generate');
    }

    /**
     * scan templates for less include
     *
     * @param oxIOutput $oOutput output
     */
    public function execute(oxIOutput $oOutput)
    {
        $lessGenerator = oxNew('RALessGeneratorService');
        $lessGenerator->generate();
        $lessCacheDir = '/' . trim(oxRegistry::get("oxConfigFile")->getVar("sCompileDir"), '/') . '/less';
        if (is_dir($lessCacheDir)) {
            $oOutput->writeLn('clear less cache...');
            foreach (new DirectoryIterator($lessCacheDir) as $file) {
                if ($file->isFile()) {
                    unlink($file->getRealPath());
                }
            }
            rmdir($lessCacheDir);
        }
    }
}
