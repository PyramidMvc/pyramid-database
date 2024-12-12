<?php
/**
 * App          : Pyramid PHP Fremework
 * Author       : Nihat Doğan
 * Email        : info@pyramid.com
 * Website      : https://www.pyramid.com
 * Created Date : 01/01/2025
 * License GPL
 *
 */
return [

	env( 'DB_CONNECTION', 'sqlite' ) => [
		'driver'                  => 'sqlite',
		'url'                     => env( 'DATABASE_URL' ),
		'database'                => env( 'DB_DATABASE', database_path( 'database.sqlite' ) ),
		'prefix'                  => '',
		'foreign_key_constraints' => env( 'DB_FOREIGN_KEYS', true ),
	],

	env( 'DB_CONNECTION', 'mysql' ) => [
		'driver'         => 'mysql',
		'url'            => env( 'DATABASE_URL' ),
		'host'           => env( 'DB_HOST', '127.0.0.1' ),
		'port'           => env( 'DB_PORT', '3306' ),
		'database'       => env( 'DB_DATABASE', 'torus' ),
		'username'       => env( 'DB_USERNAME', 'torus' ),
		'password'       => env( 'DB_PASSWORD', '' ),
		'unix_socket'    => env( 'DB_SOCKET', '' ),
		'charset'        => 'utf8mb4',
		'collation'      => 'utf8mb4_unicode_ci',
		'prefix'         => '',
		'prefix_indexes' => true,
		'strict'         => true,
		'engine'         => null,
		'options'        => extension_loaded( 'pdo_mysql' ) ? array_filter( [
			PDO::MYSQL_ATTR_SSL_CA => env( 'MYSQL_ATTR_SSL_CA' ),
		] ) : [],
	],

	env( 'DB_CONNECTION', 'pgsql' ) => [
		'driver'         => 'pgsql',
		'url'            => env( 'DATABASE_URL' ),
		'host'           => env( 'DB_HOST', '127.0.0.1' ),
		'port'           => env( 'DB_PORT', '5432' ),
		'database'       => env( 'DB_DATABASE', 'torus' ),
		'username'       => env( 'DB_USERNAME', 'torus' ),
		'password'       => env( 'DB_PASSWORD', '' ),
		'charset'        => 'utf8',
		'prefix'         => '',
		'prefix_indexes' => true,
		'search_path'    => 'public',
		'sslmode'        => 'prefer',
	],

	env( 'DB_CONNECTION', 'sqlsrv' ) => [
		'driver'         => 'sqlsrv',
		'url'            => env( 'DATABASE_URL' ),
		'host'           => env( 'DB_HOST', 'localhost' ),
		'port'           => env( 'DB_PORT', '1433' ),
		'database'       => env( 'DB_DATABASE', 'torus' ),
		'username'       => env( 'DB_USERNAME', 'torus' ),
		'password'       => env( 'DB_PASSWORD', '' ),
		'charset'        => 'utf8',
		'prefix'         => '',
		'prefix_indexes' => true,
		// 'encrypt' => env('DB_ENCRYPT', 'yes'),
		// 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
	],


];
