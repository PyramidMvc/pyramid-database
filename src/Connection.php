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

namespace Pyramid;

class Connection {

	public static function conn() {
		try {

			$host      = config( 'Database.mysql.host' );
			$usernamne = config( 'Database.mysql.username' );
			$password  = config( 'Database.mysql.password' );
			$database  = config( 'Database.mysql.database' );
			$charset   = config( 'Database.mysql.charset' );
			$port      = config( 'Database.mysql.port' );

			$connect = new \PDO( "mysql:host=$host;port=$port;dbname=$database;charset=$charset", $usernamne, $password );
			$connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT); // Hatalar sessizce işlenebilir
			$connect->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC); // Varsayılan fetch modu: Associative array
			$connect->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); // Hazırlıklı sorgu gerçek şekilde çalıştırılsın
			$connect->setAttribute(\PDO::ATTR_PERSISTENT, true); // Kalıcı bağlantı
			$connect->setAttribute(\PDO::ATTR_TIMEOUT, 5); // Zaman aşımı süresi

			return $connect;
		} catch ( PDOException $ex ) {
			die( $ex->getMessage() );
		}

	}

}