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

class Validator {
	// Gelen doğrulama kuralları
	protected $rules = [];
	protected $errors = [];

	// Kuralları al
	public function __construct( $rules ) {

		$this->rules = $rules;
	}

	// Doğrulama işlemini yap
	public function validate( $data ) {

		foreach ( $this->rules as $field => $rule ) {
			$value = isset( $data[ $field ] ) ? $data[ $field ] : null;

			// Kuralı kontrol et
			if ( is_array( $rule ) ) {

				foreach ( $rule as $r ) {
					$this->applyRule( $r, $field, $value, $data );
					session( "old_$field", $value );
				}
			} else {
				session( "old_$field", $value );
				$this->applyRule( $rule, $field, $value, $data );
			}
		}

		return empty( $this->errors );
	}

	// Bir kuralı uygula
	protected function applyRule( $rule, $field, $value, $data ) {

		switch ( strval( $rule ) ) {
			case 'required':
				if ( empty( $value ) ) {
					$this->addError( $field, "$field alanı zorunludur." );
				}
				break;

			case 'email':
				if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
					$this->addError( $field, "$field alanı geçerli bir e-posta olmalıdır." );
				}
				break;

			case ( strpos( $rule, 'unique:' ) === 0 ):
				$table = explode( ':', $rule )[1];
				$stmt  = Connection::conn()->prepare( "SELECT * FROM $table WHERE $field = :$field" );
				$stmt->bindParam( ":$field", $value, \PDO::PARAM_STR );
				$stmt->execute();
				$unique = $stmt->fetch( \PDO::FETCH_ASSOC );
				if ( $unique ) {
					$this->addError( $field, "Bu $field zaten kullanımda." );
				}
				break;

			case 'numeric':
				if ( ! is_numeric( $value ) ) {
					$this->addError( $field, "$field alanı sayısal olmalıdır." );
				}
				break;

			case ( strpos( $rule, 'min:' ) === 0 ):

				// Min değeri almak için ":" karakterinden sonrasını alıyoruz
				$minLength = (int) explode( ':', $rule )[1];
				if ( strlen( $value ) < $minLength ) {
					$this->addError( $field, "$field alanı en az $minLength karakter uzunluğunda olmalıdır." );
				}
				break;

			case ( strpos( $rule, 'max:' ) === 0 ):
				// Max değeri almak için ":" karakterinden sonrasını alıyoruz
				$maxLength = (int) explode( ':', $rule )[1];

				if ( strlen( $value ) > $maxLength ) {
					$this->addError( $field, "$field alanı en fazla $maxLength karakter uzunluğunda olmalıdır." );
				}
				break;

			case 'in':
				// in_array değeri
				if ( ! in_array( $value, $rule ) ) {
					$this->addError( $field, "$field $rule Seçilen değer geçersiz." );
				}
				break;

			case 'numeric':
				// numeric değeri
				if ( ! is_numeric( $value ) ) {
					$this->addError( $field, "$field $rule Bu alan bir sayı olmalıdır." );
				}
				break;

			case 'alpha':
				// alpha değeri
				if ( ! ctype_alpha( $value ) ) {
					$this->addError( $field, "$field Bu alan yalnızca alfabetik karakterler içermelidir sayı yazılamaz." );
				}
				break;

			case 'regex':
				// regex değeri
				if ( ! preg_match( $value, $value ) ) {
					$this->addError( $field, "$field Bu alan $rule biçiminde olmalı biçim geçersiz." );
				}
				break;

			case ( strpos( $rule, 'same:' ) === 0 ):
				// same değeri karşılaştırma
				$same = explode( ':', $rule )[1];

				if ( $value != $same ) {
					$this->addError( $field, "$field Bu alan eşleşmiyor: ." );
				}
				break;

			case ( strpos( $rule, 'confirmed:' ) === 0 ):
				// confirmed değeri

				// confirmed değeri karşılaştırma
				$confirmed = explode( ':', $rule )[1];

				if ( $value != $confirmed ) {
					$this->addError( $field, "$field Bu alan eşleşmiyor: ." );
				}
				break;


			case 'alpha_num':
				// alpha_num değeri
				if ( ! ctype_alnum( $value ) ) {
					$this->addError( $field, "$field Bu alan yalnızca harf ve rakamlardan oluşmalıdır." );
				}
				break;

			case 'date':
				// date değeri
				$date = strtotime( $value );
				if ( ! $date ) {
					$this->addError( $field, "$field Bu alan geçerli bir tarih olmalıdır." );
				}
				break;

			case 'date_format':
				// date_format değeri

				$format = "Y-m-d";
				$d      = \DateTime::createFromFormat( $format, $value );

				if ( ! $d || $d->format( $format ) !== $value ) {
					$this->addError( $field, "$field Bu alan $format formatla eşleşmelidir." );
				}
				break;

			case 'date_time_format':
				// date_format değeri

				$format = "Y-m-d H:i:s";
				$d      = \DateTime::createFromFormat( $format, $value );

				if ( ! $d || $d->format( $format ) !== $value ) {
					$this->addError( $field, "$field Bu alan $format formatla eşleşmelidir." );
				}
				break;


			// Diğer kurallar eklenebilir...
		}
	}

	// Hata mesajlarını ekle
	protected function addError( $field, $message ) {
		if ( ! isset( $this->errors[ $field ] ) ) {
			$this->errors[ $field ] = [];
		}
		$this->errors[ $field ][] = $message;
	}

	// Hataları al
	public function errors() {
		return json_decode( json_encode( $this->errors ) );
	}
}

?>