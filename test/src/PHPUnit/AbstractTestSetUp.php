<?php
/*
 * SPDX-FileCopyrightText: 2023 Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * SPDX-License-Identifier: AGPL-3.0-only
 */

declare(strict_types=1);

namespace Ruga\Dms\Test\PHPUnit;

use Laminas\ServiceManager\ServiceManager;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;
use PHPUnit\Framework\TestCase;

/**
 * Common setup for all PHPUnit tests that use the common configuration and a container.
 * Loads configuration and creates a service manager.
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
abstract class AbstractTestSetUp extends \Ruga\Db\PHPUnit\AbstractTestSetUp
{
    private function rm(string $dir)
    {
        $dir=realpath($dir);
        foreach (glob($dir . "/*") as $file) {
            if (is_dir($file)) {
                $this->rm($file);
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }
    
    
    
    
    protected function setUp(): void
    {
        foreach (glob(__DIR__ . "/../../tmp/*") as $file) {
            unlink($file);
        }
//        foreach (glob(__DIR__ . "/../../data/files/*") as $file) {
//            unlink($file);
//        }
        foreach (glob(__DIR__ . "/../../data/libraries/*") as $file) {
            unlink($file);
        }
        
        
        $this->rm(__DIR__ . "/../../data/files/");
        
        
//        foreach (glob(__DIR__ . "/../data/config_files/*") as $file) {
//            unlink($file);
//        }
//        foreach (glob(__DIR__ . "/../data/library2/*") as $file) {
//            unlink($file);
//        }
//        foreach (glob(__DIR__ . "/../data/library3/*") as $file) {
//            unlink($file);
//        }
        
//        $this->rm(__DIR__ . "/../data/library1/");
        
        parent::setUp();
    }
    
    
    
    /**
     * Return the test specific merged config.
     *
     * @return array
     */
    public function configProvider()
    {
        $config = new ConfigAggregator(
            [
                new \Ruga\Db\ConfigProvider(),
                new \Ruga\Dms\ConfigProvider(),
                new PhpFileProvider(__DIR__ . "/../../config/config.php"),
                new PhpFileProvider(__DIR__ . "/../../config/config.local.php"),
            ], null, []
        );
        return $config->getMergedConfig();
    }
    
    
}
