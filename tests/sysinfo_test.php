<?php
/**
 * ezcSystemInfoTest
 * 
 * @package SystemInformation
 * @version //autogen//
 * @subpackage Tests
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Test suite for class.
 * 
 * @package SystemInformation
 * @subpackage Tests
 */
class ezcSystemInfoTest extends ezcTestCase
{
    public function testSystemInfoCpuTypeTest()
    {
        $info = ezcSystemInfo::getInstance();
        $cpuType = $info->cpuType;
        $haveCpuVendor = preg_match( '/AMD|Intel|Cyrix/', $cpuType ) ? true : false;

        if( !is_string( $cpuType ) || $cpuType=='' || !$haveCpuVendor )
        {
            self::fail('CPU type was not determined correctly');
        }
    }

    public function testSystemInfoCpuSpeedTest()
    {
        $info = ezcSystemInfo::getInstance();
        $cpuSpeed = $info->cpuSpeed;
        $haveCpuSpeed = preg_match( '/([0-9]+)(\.)?([0-9]+)$/', $cpuSpeed ) ? true : false;
        
        if( !is_string($cpuSpeed) || $cpuSpeed=='' || !$haveCpuSpeed ) 
        {
            self::fail('CPU speed was not determined correctly');
        }
    }

    public function testSystemInfoCpuUnitTest()
    {
        $info = ezcSystemInfo::getInstance();
        $cpuUnit = $info->cpuUnit;
        
        if( $cpuUnit != 'MHz' && $cpuUnit !='GHz' ) 
        {
            self::fail('CPU speed unit was not determined correctly');
        }
    }

    public function testSystemInfoOsNameTest()
    {
        $info = ezcSystemInfo::getInstance();
        $osName = $info->osName;

        $haveOsName = preg_match( '/Linux|FreeBSD|Windows|Mac/', $osName ) ? true : false;
        
        if( !$haveOsName ) 
        {
            self::fail('OS name was not determined correctly');
        }
    }

    public function testSystemInfoOsTypeTest()
    {
        $info = ezcSystemInfo::getInstance();
        $osType = $info->osType;

        $haveOsType = preg_match( '/unix|win32|mac/', $osType ) ? true : false;
        
        if( !$haveOsType ) 
        {
            self::fail('OS type was not determined correctly');
        }
    }

    public function testSystemInfoMemorySizeTest()
    {
        $info = ezcSystemInfo::getInstance();
        $memorySize = $info->memorySize;
        
        if ( substr( php_uname( 's' ), 0, 7 ) == 'Windows' && !extension_loaded("win32ps") )
        {
            // scanning of Total Physical memory not implemented for Windows
            // without php_win32ps.dll extention installed
            if ( $memorySize != null )
            {
                self::fail('OS memory size should be null in Windows when win32ps extention is not installed in PHP');
            }
            return;
        }
        
        if ( !is_int( $memorySize ) || $memorySize == 0 || $memorySize % 1024 != 0 )
        {
            self::fail('OS memory size was not determined correctly');
        }
    }

    public function testSystemInfoFileSystemTypeTest()
    {
        $info = ezcSystemInfo::getInstance();
        $fileSystemType = $info->fileSystemType;

        $haveFileSysType = preg_match( '/unix|win32/', $fileSystemType ) ? true : false;
        if( !$haveFileSysType ) 
        {
            self::fail('File System type was not determined correctly');
        }
    }

    public function testSystemInfoLineSeparatorTest()
    {
        $info = ezcSystemInfo::getInstance();
        $lineSeparator = $info->lineSeparator;

        if( $lineSeparator != "\n" && $lineSeparator != "\r\n" && $lineSeparator != "\r" )
        {
            self::fail('Line separator was not determined correctly');
        }
    }

    public function testSystemInfoBackupFileNameTest()
    {
        $info = ezcSystemInfo::getInstance();
        $backupFileName = $info->backupFileName;

        if( $backupFileName != "~" && $backupFileName != ".bak" )
        {
            self::fail('Backup file name was not determined correctly');
        }
    }

    public function testSystemInfoPhpVersionTest()
    {
        $phpVersion = ezcSystemInfo::phpVersion();
        $waitVersion = explode( '.', phpVersion() );

        self::assertEquals( 
            $phpVersion,
            $waitVersion,
            'Php version was not determined correctly'
        );
        unset( $phpVersion );
        $info = ezcSystemInfo::getInstance();
        $phpVersion = $info->phpVersion;
        self::assertEquals( 
            $phpVersion,
            $waitVersion,
            'Php version was not determined correctly'
        );
    }

    public function testSystemInfoIsShellExecutionTest()
    {
        $info = ezcSystemInfo::getInstance();
        $isShellExecution = $info->isShellExecution();

        self::assertEquals( 
            $isShellExecution,
            true,
            'Execution from shell was not determined correctly'
        );

        unset ( $isShellExecution );
        $isShellExecution = ezcSystemInfo::isShellExecution();
        self::assertEquals( 
            $isShellExecution,
            true,
            'Execution from shell was not determined correctly'
        );

    }

    public function testSystemInfoPhpAcceleratorTest()
    {
        $testSample = null;
        $accelerator = ezcSystemInfo::phpAccelerator();

        if ( isset( $GLOBALS['_PHPA'] ) )
        {
            $testSample = new ezcSystemInfoAccelerator(
                    "ionCube PHP Accelerator",          // name
                    "http://www.php-accelerator.co.uk", // url
                    $GLOBALS['_PHPA']['ENABLED'],       // isEnabled
                    $GLOBALS['_PHPA']['iVERSION'],      // version int
                    $GLOBALS['_PHPA']['VERSION']        // version string
            );
            self::assertEquals( $accelerator, $testSample, 'PHP Accelerator not determined correctly' );
        }
        else if ( extension_loaded( "Turck MMCache" ) )
        {
            $testSample = new ezcSystemInfoAccelerator(
                "Turck MMCache",                        // name
                "http://turck-mmcache.sourceforge.net", // url
                true,                                   // isEnabled
                false,                                  // version int
                false                                   // version string
            );
            self::assertEquals( $accelerator, $testSample, 'PHP Accelerator not determined correctly' );
        }
        else if ( extension_loaded( "eAccelerator" ) )
        {
            $testSample = new ezcSystemInfoAccelerator(
                "eAccelerator",                                     // name            
                "http://sourceforge.net/projects/eaccelerator/",    // url
                true,                                               // isEnabled
                false,                                              // version int
                phpversion('eAccelerator')                          // version string
            );
            self::assertEquals( $accelerator, $testSample, 'PHP Accelerator not determined correctly' );
        }
        else if ( extension_loaded( "apc" ) )
        {
            $testSample = new ezcSystemInfoAccelerator(
                 "APC",                                  // name
                 "http://pecl.php.net/package/APC",      // url
                 (ini_get( 'apc.enabled' ) != 0),        // isEnabled
                 false,                                  // version int
                 phpversion( 'apc' )                     // version string
              );
            self::assertEquals( $accelerator, $testSample, 'PHP Accelerator not determined correctly' );
        } 
        else if ( extension_loaded( "Zend Performance Suite" ) )
        {
            $testSample = new ezcSystemInfoAccelerator(
                    "Zend WinEnabler (Zend Performance Suite)",                // name
                    "http://www.zend.com/store/products/zend-win-enabler.php", // url
                    true,                                                      // isEnabled
                    false,                                                     // version int
                    false                                                      // version string
                );
            self::assertEquals( $accelerator, $testSample, 'PHP Accelerator not determined correctly' );
        }
        else 
        {
            self::assertEquals( $accelerator, null, 'phpAccelerator() should return null' );
        }
    }
    
    public static function suite()
    {
        return new ezcTestSuite( "ezcSystemInfoTest" );
    }
}
?>