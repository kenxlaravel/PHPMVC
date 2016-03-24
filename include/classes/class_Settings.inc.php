<?php

class Settings {

	public static function getSettingValue($setting) {

		if ( isset($setting) ) {

			$sth = Connection::getHandle()->prepare('SELECT `value`, `type` FROM bs_config WHERE setting = :setting LIMIT 1');

    		if( $sth->execute(array(':setting'=>$setting)) ) {

                $row = $sth->fetch();
                $value = $row['value'];
                $type = ($row['type'] == "int") ? "integer" : $row['type'];

                settype($value, $type);
            }
		}

		return $value;

	}

}