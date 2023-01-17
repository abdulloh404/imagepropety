<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations
     * and Seeds directories.
     *
     * @var string
     */
    public $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to
     * use if no other is specified.
     *
     * @var string
     */
    public $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array
     */
    public $default = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'gold',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect' => false,
        'DBDebug'  => false,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];

    /**
     * This database connection is used when
     * running PHPUnit database tests.
     *
     * @var array
     */
    public $tests = [
        'DSN'      => '',
        'hostname' => '143.198.212.85',
        'username' => 'home',
        'password' => 'iBAvLY1yzzZu68Yc',
        'database' => 'gold',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect' => false,
        'DBDebug'  => false,
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];
	
    public function __construct() {
		
        parent::__construct();

		if( false ) {
			$this->tests = [
				'DSN'      => '',
				'hostname' => '143.198.212.85',
				'username' => 'home',
				'password' => 'iBAvLY1yzzZu68Yc',
				'database' => 'gold',
				'DBDriver' => 'MySQLi',
				'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
				'pConnect' => false,
				'DBDebug'  => false,
				'charset'  => 'utf8',
				'DBCollat' => 'utf8_general_ci',
				'swapPre'  => '',
				'encrypt'  => false,
				'compress' => false,
				'strictOn' => false,
				'failover' => [],
				'port'     => 3306,
			];
		}
		else {
			
			$this->tests = [
				'DSN'      => '',
				'hostname' => 'localhost',
				'username' => 'root',
				'password' => '1',
				'database' => 'goldcity',
				'DBDriver' => 'MySQLi',
				'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
				'pConnect' => false,
				'DBDebug'  => false,
				'charset'  => 'utf8',
				'DBCollat' => 'utf8_general_ci',
				'swapPre'  => '',
				'encrypt'  => false,
				'compress' => false,
				'strictOn' => false,
				'failover' => [],
				'port'     => 3306,
			];
		}
		
		
		
    }
}