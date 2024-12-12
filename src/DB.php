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

use Pyramid\Connection;

class DB {

	public static function where() {
		$data = func_get_args();
		$data = implode( $data );
		if ( $data ):
			return " WHERE $data ";
		endif;
	}

	public static function like() {
		$data = func_get_args();
		$data = implode( $data );
		if ( $data ):
			return " LIKE $data ";
		endif;
	}

	public static function select() {
		$data         = func_get_args(); // gelen veriyi dizi halinde alıyoruz
		$table        = $data[0][0];
		$table_select = $data[0][1];
		$where        = DB::where( @$data[0][2] );
		$like        = DB::like( @$data[0][3] );
		$sql          = "SELECT $table_select FROM $table $where $like ";

		$data = Connection::conn()->prepare( $sql );
		$data->execute();
		$data = $data->fetchAll( \PDO::FETCH_OBJ );

		return $data;
	}

	public static function find() {
		$data         = func_get_args(); // gelen veriyi dizi halinde alıyoruz
		$table        = $data[0][0];
		$id        = $data[0][1];

		$sql          = "SELECT * FROM $table WHERE id=$id LIMIT 1";

		$data = Connection::conn()->prepare( $sql );
		$data->execute();
		$data = $data->fetchAll( \PDO::FETCH_OBJ );

		return $data;
	}

	public static function insert() {
		$data   = func_get_args(); // gelen veriyi dizi halinde alıyoruz
		$values = [];
		$params = [];
		$keys   = [];
		foreach ( $data[0][1] as $key => $value ) {
			$values[] = "?";
			$params[] = $value;
			$keys[]   = $key;
		}
		$table    = $data[0][0];
		$val      = implode( ', ', $keys );
		$sql      = "INSERT INTO $table ($val) VALUES ";
		$sql      .= '(' . implode( ', ', $values ) . ')';
		$response = Connection::conn()->prepare( $sql );
		if ( $response->execute( $params ) ):
			return true;
		else:
			return false;
		endif;

	}


	public static function update() {
		$data = func_get_args();

		$setParts = [];
		$params   = [];

		$table = $data[0][0];
		$sql   = "UPDATE $table SET ";

		foreach ( $data[0][1] as $key => $value ) {
			$setParts[] = "$key = ?";
			$params[]   = $value;
		}

		$sql .= implode( ", ", $setParts );
		$sql .= " WHERE id = ?";
		$params[] = $data[0][2][0];

		$response = Connection::conn()->prepare( $sql );

		if ( $response->execute( $params ) ):
			return true;
		else:
			return false;endif;
	}


	public static function delete() {
		$data = func_get_args();

		$params = [];
		$table = $data[0][0];
		$sql   = "DELETE FROM $table WHERE id = ?";

		$params[] = $data[0][1][0];

		$response = Connection::conn()->prepare( $sql );

		if ( $response->execute( $params ) ):
			return true;
		else:
			return false;
		endif;
	}


}