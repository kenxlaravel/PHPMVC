<?php

class Countries{


	private $dbh;



	public function __construct() {
		//Establish a database connection
		$this->dbh = Connection::getHandle();
	}



	public function CountryList(){
		$stmt_country=$this->dbh->prepare("SELECT ifnull(countries_name, '') AS countries_name,
												  ifnull(countries_iso_code_2, '') AS countries_iso_code_2,
												  ifnull(countries_iso_code_3, '') AS countries_iso_code_3
										   FROM bs_countries
										   ORDER BY countries_id");
		$stmt_country->execute();
		while($country_row=$stmt_country->fetch(PDO::FETCH_ASSOC)){
			$country[]=$country_row;
		}
		return $country;
	}



	public function CountryCodeList($countries_iso_code) {

		$country_code = NULL;

		if( isset($countries_iso_code) ) {

			$stmt_country_list = $this->dbh->prepare(

				"SELECT ifnull(countries_name, '') AS countries_name,
					ifnull(countries_iso_code_2, '') AS countries_iso_code_2,
					ifnull(countries_iso_code_3, '') AS countries_iso_code_3
				FROM bs_countries WHERE countries_iso_code_2 = :countries_iso_code"
			);

			$stmt_country_list->execute(array (":countries_iso_code" => $countries_iso_code));

			while ($country_row = $stmt_country_list->fetch(PDO::FETCH_ASSOC)) {

				$country_code = $country_row;
			}
		}

		return $country_code;
	}
}