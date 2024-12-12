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

class QueryBuilder {
	protected static $table;
	protected static $query;
	protected static $whereQuery = "";
	protected static $orderQuery = "";
	protected static $limitQuery = "";
	protected static $offsetQuery = "";
	protected static $groupByQuery = "";
	protected static $havingQuery = "";
	protected static $innerJoinQuery = "";
	protected static $leftJoinQuery = "";
	protected static $rightJoinQuery = "";
	protected static $uionQuery = "";
	protected static $unionAllQuery = "";
	protected static $bindings = [];
	protected static $id;

	public static function plural( $string ) {
		$pattern     = '/[^a-zA-Z0-9]/';
		$replacement = '_';
		$result      = preg_replace( $pattern, $replacement, $string );

		return $result;
	}

	public static function snake( $string ) {
		$string = strtolower( $string );
		$string = preg_replace( '/([a-z])([A-Z])/', '$1_$2', $string );
		$string = str_replace( ' ', '_', $string );

		return $string;
	}

	public static function setTable( $nesne ) {
		QueryBuilder::$table = QueryBuilder::plural( QueryBuilder::snake( $nesne ) );
	}



////////////////////////////////TORUS QERYBUILDER////////////////////////////////////////


	// Statik fonksiyon: INSERT işlemi
	public static function insert( $data ) {
		// Verilen veri dizisindeki sütunları ayarla
		$columns      = implode( ", ", array_keys( $data ) );
		$placeholders = ":" . implode( ", :", array_keys( $data ) ); // :column_name şeklinde bağlama yapacağız
		// Sorguyu oluştur
		self::$query = "INSERT INTO " . self::$table . " ($columns) VALUES ($placeholders)";
		// Verileri bağla
		$stmt = Connection::conn()->prepare( self::$query );
		// Veriyi bağla ve sorguyu çalıştır
		foreach ( $data as $key => $value ) {
			$stmt->bindValue( ":$key", $value );
		}
		// Sorguyu çalıştır ve başarı durumunu döndür
		if ( $stmt->execute() ) {
			return true;
		} else {
			return null;
		}


	}

	////////////////////////////MULTIPLE INSERT INTO///////////////////////////////
	public function insertMultiple( $data ) {
		// Verilen veri dizisindeki sütunları ayarla
		$columns      = implode( ", ", array_keys( $data[0] ) );
		$placeholders = ":" . implode( ", :", array_keys( $data[0] ) ); // :column_name şeklinde bağlama yapacağız
		// Sorguyu oluştur
		self::$query = "INSERT INTO " . self::$table . " ($columns) VALUES ";
		// Her veri için VALUES kısmını oluştur
		$values = [];
		foreach ( $data as $row ) {
			$values[] = "($placeholders)";
		}
		self::$query .= implode( ", ", $values ); // tüm veri için placeholders eklenir
		// Sorguyu hazırlayın
		$stmt = $this->pdo->prepare( self::$query );
		// Her bir veriyi bağla
		$i = 1;
		foreach ( $data as $row ) {
			foreach ( $row as $key => $value ) {
				$stmt->bindValue( ":$key$i", $value );
			}
			$i ++;
		}
		// Sorguyu çalıştır ve başarı durumunu döndür
		$result = $stmt->execute();

		return $result ? new self( $result ) : null;
	}
	///////////////////////////////////////////////////////////


	// Statik fonksiyon: UPDATE işlemi
	public static function update( $data ) {

		// Güncellenmek istenen verilerin sütun adları ve değerleri
		$setColumns = [];
		foreach ( $data as $column => $value ) {
			$setColumns[] = "$column = :$column";
		}
		$setClause = implode( ", ", $setColumns );
		// Sorguyu oluştur
		self::$query = "UPDATE " . self::$table . " SET $setClause ";

		if ( self::$whereQuery ) {
			self::$query .= self::$whereQuery;
		}


		$stmt = Connection::conn()->prepare( self::$query );
		// Veriyi bağla

		foreach ( $data as $column => $value ) {
			$stmt->bindValue( ":$column", $value );
		}

		// WHERE koşulundaki parametreyi bağla
		if ( isset( self::$bindings ) ) {
			foreach ( self::$bindings as $param => $value ) {
				$stmt->bindValue( $param, $value );
			}
		}

//		 Sorguyu çalıştır ve başarı durumunu döndür
		$result = $stmt->execute();

		return $result ? new self() : null;
	}


	public static function find( $id ) {
		self::$id = $id;
		// Sorguyu hazırla
		self::$query = "SELECT * FROM " . self::$table . " WHERE id = :id LIMIT 1";

		// Sorguyu hazırlayın
		$stmt = Connection::conn()->prepare( self::$query );

		// Parametreyi bağla
		$stmt->bindValue( ':id', $id, \PDO::PARAM_INT );

		// Sorguyu çalıştır
		$stmt->execute();

		// Eğer kayıt varsa, döndür; yoksa null döndür
		$result = $stmt->fetch( \PDO::FETCH_OBJ );

		return $result ? new self( $result ) : null;
	}

	public static function delete() {
		// Sorguyu hazırla
		self::$query = "DELETE FROM " . self::$table . " WHERE id = :id";

		// Sorguyu hazırlayın
		$stmt = Connection::conn()->prepare( self::$query );

		// Parametreyi bağla
		$stmt->bindValue( ':id', self::$id, \PDO::PARAM_INT );

		// Sorguyu çalıştır ve başarı durumuna göre geri dön
		$result = $stmt->execute();

		return $result ? new self( $result ) : null;
	}


	public static function get() {
		$stmt = Connection::conn()->prepare( self::$query );
		$stmt->execute( self::$bindings );

		return $stmt->fetchAll( \PDO::FETCH_OBJ );
	}


/////////////////////////////////CONDITIONS/////////////////////////////////////////

	// SELECT sütunlarını belirt
	public static function select( $columns = '*' ) {
		self::$query = "SELECT {$columns} FROM " . self::$table;
		if ( self::$whereQuery ) {
			self::$query .= self::$whereQuery;
		}
		if ( self::$orderQuery ) {
			self::$query .= self::$orderQuery;
		}
		if ( self::$limitQuery ) {
			self::$query .= self::$limitQuery;
		}
		if ( self::$offsetQuery ) {
			self::$query .= self::$offsetQuery;
		}
		if ( self::$groupByQuery ) {
			self::$query .= self::$groupByQuery;
		}
		if ( self::$havingQuery ) {
			self::$query .= self::$havingQuery;
		}
		if ( self::$innerJoinQuery ) {
			self::$query .= self::$innerJoinQuery;
		}
		if ( self::$leftJoinQuery ) {
			self::$query .= self::$leftJoinQuery;
		}
		if ( self::$rightJoinQuery ) {
			self::$query .= self::$rightJoinQuery;
		}
		if ( self::$uionQuery ) {
			self::$query .= self::$uionQuery;
		}
		if ( self::$unionAllQuery ) {
			self::$query .= self::$unionAllQuery;
		}

		return new self;
	}


	// WHERE Koşulu
	public static function where( $column, $operator, $value ) {

		// İlk WHERE eklenmişse, 'AND' ile devam et
		if ( strpos( self::$whereQuery, 'WHERE' ) === false ) {
			self::$whereQuery = " WHERE {$column} {$operator} :value_1";

		} else {
			// Dinamik parametre ekleyerek AND ile devam et
			$bindIndex        = count( self::$bindings ) + 1;
			self::$whereQuery .= " AND {$column} {$operator} :value_{$bindIndex}";

		}

		self::$bindings[ ":value_" . ( count( self::$bindings ) + 1 ) ] = $value;

		return new self;
	}

	// ORDER BY Koşulu
	public static function orderBy( $column, $direction = 'ASC' ) {
		if ( strpos( self::$orderQuery, 'ORDER BY' ) === false ) {
			self::$orderQuery = " ORDER BY {$column} {$direction}";
		} else {
			self::$orderQuery .= ", {$column} {$direction}";
		}

		return new self;
	}

	// LIMIT Koşulu
	public static function limit( $limit ) {
		self::$limitQuery = " LIMIT {$limit}";

		return new self;
	}

	// OFFSET Koşulu (LIMIT ile birlikte kullanılır)
	public static function offset( $offset ) {
		self::$offsetQuery = " OFFSET {$offset}";

		return new self;
	}

	// GROUP BY Koşulu
	public static function groupBy( $columns ) {
		self::$groupByQuery = " GROUP BY {$columns}";

		return new self;
	}

	// HAVING Koşulu (GROUP BY ile kullanılır)
	public static function having( $column, $operator, $value ) {
		// HAVING eklerken, WHERE'den farklı bir parametre kullanılır.
		$bindIndex                                                      = count( self::$bindings ) + 1;
		self::$havingQuery                                              = " HAVING {$column} {$operator} :value_{$bindIndex}";
		self::$bindings[ ":value_" . ( count( self::$bindings ) + 1 ) ] = $value;

		return new self;
	}

	// JOIN Koşulu (INNER JOIN örneği)
	public static function join( $table, $on ) {
		self::$innerJoinQuery = " INNER JOIN {$table} ON {$on}";

		return new self;
	}

	// LEFT JOIN Koşulu
	public static function leftJoin( $table, $on ) {
		self::$leftJoinQuery = " LEFT JOIN {$table} ON {$on}";

		return new self;
	}

	// RIGHT JOIN Koşulu
	public static function rightJoin( $table, $on ) {
		self::$rightJoinQuery = " RIGHT JOIN {$table} ON {$on}";

		return new self;
	}

	// UNION Koşulu
	public static function union( $query ) {
		self::$uionQuery = " UNION {$query}";

		return new self;
	}

	// UNION ALL Koşulu
	public static function unionAll( $query ) {
		self::$unionAllQuery = " UNION ALL {$query}";

		return new self;
	}
////////////////////////////////END//////////////////////////////////


}