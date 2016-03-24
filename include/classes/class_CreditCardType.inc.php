<?php


class CreditCardType
{


    private $dbh;



    /**
     * Constructor
     */
    public function __construct() {

        //Establish a database connection
        $this->setDatabase();

    }



    /**
     * This function checks to make sure we have a PDO instance, and sets our class variable
     * If we do not, we instantiate a new connection
     */
    private function setDatabase() {

        global $dbh;

        if ($dbh instanceof PDO) {
            $this->dbh = $dbh;
        } else {
            $Connection = new Connection();
            $this->dbh = $Connection->PDO_Connection();
        }

    }



    function validateCC($ccnum) {
        $ccnum=ereg_replace('[-[:space:]]', '',$ccnum);
        $type=$this->checkCardType($ccnum);
        $valid=$this->checkNumber($ccnum);
        return array($type,$valid);
    }



	function checkCardType($cardnumber) {

   		$cardtype = "UNKNOWN";
		$len = strlen($cardnumber);
   		if($len==15 && substr($cardnumber,0,1)=='3')                     { $cardtype = "Amex"; }
		elseif ($len==16 && substr($cardnumber,0,4)=='6011')             { $cardtype = "Discover"; }
		elseif ($len==16 && substr($cardnumber,0,1)=='5')                { $cardtype = "Mastercard"; }
	    elseif (($len==16 || $len==13) && substr($cardnumber,0,1)=='4')  { $cardtype = "Visa"; }

	   	return ($cardtype );
	}


	function checkNumber($cardnumber) {

        $dig = $this->toCharArray($cardnumber);
        $numdig = sizeof ($dig);
        $j = 0;

        for ($i=($numdig-2); $i>=0; $i-=2) {
            $dbl[$j]=$dig[$i]*2;
            $j++;
        }

        $dblsz = sizeof($dbl);
        $validate = 0;

        for($i=0;$i<$dblsz;$i++) {

            $add=$this->toCharArray($dbl[$i]);

            for($j=0;$j<sizeof($add);$j++) {
                $validate += $add[$j];
            }

            $add='';
        }


        for($i=($numdig-1);$i>=0;$i-=2) {
            $validate +=$dig[$i];
        }

        if(substr($validate,-1,1)=='0')
            return 1;
        else
            return 0;
	}



    // takes a string and returns an array of characters
	function toCharArray($input) {
        $len=strlen($input);
        for($j=0;$j<$len;$j++) {
            $char[$j]=substr($input,$j,1);
    	}

    	return ($char);
	}



	function GetUpdateCcType() {

        $sql = $this->dbh->prepare("SELECT id AS id,
                                           orders_id AS orders_id,
                                           ccNum AS ccNum,
                                           ccType AS ccType
                                    FROM bs_credit_card
                                    WHERE ccType=''
                                    ORDER BY id");


		while ($cc_type_row = $sql->fetch(PDO::FETCH_ASSOC)) {

            $number=base64_decode($cc_type_row['ccNum']);
            list($type,$valid)=$this->validateCC($number);

            if($valid) {
    			$cc_type_code=base64_encode($type);

                $sql2 = $this->dbh->prepare("SELECT orders_id AS orders_id,
                                                    customers_id AS customers_id
                                             FROM bs_orders
                                             WHERE orders_id = ?
                                             AND order_type = 'website' ");
                $sql2->execute(array($cc_type_row['orders_id']));
                $order_row = $sql2->fetch(PDO::FETCH_ASSOC);

    			if($order_row) {

                    $sql3 = $this->dbh->prepare("UPDATE bs_orders SET ccType = ? WHERE orders_id = ?");
                    $sql3->execute(array($type, $cc_type_row['orders_id']));

                    $sql4 = $this->dbh->prepare("UPDATE bs_credit_card SET ccType = ? WHERE orders_id = ?");
                    $sql4->execute(array($cc_type_code, $cc_type_row['orders_id']));

    			}
			}
		}
	}

}//end of classs