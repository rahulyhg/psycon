<?php

/*==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-*/
//                                                            //
//  Name: ECHOPHP v1.7.1 06-24-2004                           //
//  Description: PHP Class used to interface with             //
//               ECHO (http://www.echo-inc.com).              //
//  Requirements: cURL (if PHP < 4.3) - http://curl.haxx.se/  //
//                OpenSSL - http://www.openssl.org            //
//  Refer to ECHO's documentation for more info:              //
//                                                            //
//  http://www.openecho.com/echo_gateway_guide.html           //
//  https://wwws.echo-inc.com/ISPGuide-Menu.asp               //
//                                                            //
//  06-24-2004 - FIX: function_exists parameter needed quotes //
//  04-20-2004 - Improvements to version_check() and          //
//               GetEchoProp() by Antone Roundy               //
//  01-29-2004 - Fixed problem when submit is called twice    //
//  01-21-2004 - Fixed warnings about $s and curl_init        //
//  12-23-2003 - Removed Openecho ECHOTYPE3 response          //
//  10-07-2003 - Removed shipping fields (discontinued)       //
//  08-26-2003 - Updated by Salim Qadeer - cleaned up code    //
//  05-16-2003 - see WHATSNEW.txt in ECHOPHP class download   //
//  03-28-2003 - updated to reflect additional status and     //
//               avs_result codes                             //
//  03-25-2003 - added error messages for missing curl/ssl    //
//  03-21-2003 - fixed issue with cURL 7.10.2 + Win2k         //
//  02-18-2003 - the what happened to my auth code release	  //
//  01-16-2003 - removed duplicate functions		  //
//  12-03-2002 - added product_description,                   //
//               purchase_order_number  		          //
//  11-18-2002 - added sales_tax				  //
//  03-12-2002 - added ec_transaction_dt                      //
//  01-16-2002 - fixed ec_account_type (typo)                 //
//  01-10-2002 - Added ec_account_type and ec_payment_type    //
//  	     for Alex ;-)                                 //
//                                                            //
/*==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-==-*/


class EchoPHP {
    // The description of these fields can be found at https://wwws.echo-inc.com/ISPGuide-Interface.asp

    var $order_type;
    var $transaction_type;
    var $merchant_echo_id;
    var $merchant_pin;
    var $isp_echo_id;
    var $isp_pin;
    var $billing_ip_address;
    var $billing_prefix;
    var $billing_name;
    var $billing_first_name;
    var $billing_last_name;
    var $billing_company_name;
    var $billing_address1;
    var $billing_address2;
    var $billing_city;
    var $billing_state;
    var $billing_zip;
    var $billing_country;
    var $billing_phone;
    var $billing_fax;
    var $billing_email;
    var $cc_number;
    var $ccexp_month;
    var $ccexp_year;
    var $counter;
    var $debug;
    var $ec_account;
    var $ec_account_type;
    var $ec_payment_type;
    var $ec_address1;
    var $ec_address2;
    var $ec_bank_name;
    var $ec_city;
    var $ec_email;
    var $ec_first_name;
    var $ec_id_country;
    var $ec_id_exp_mm;
    var $ec_id_exp_dd;
    var $ec_id_exp_yy;
    var $ec_id_number;
    var $ec_id_state;
    var $ec_id_type;
    var $ec_last_name;
    var $ec_other_name;
    var $ec_payee;
    var $ec_rt;
    var $ec_serial_number;
    var $ec_state;
    var $ec_transaction_dt;
    var $ec_zip;
    var $grand_total;
    var $merchant_email;
    var $merchant_trace_nbr;
    var $original_amount;
    var $original_trandate_mm;
    var $original_trandate_dd;
    var $original_trandate_yyyy;
    var $original_reference;
    var $product_description;
    var $purchase_order_number;
    var $sales_tax;
    var $track1;
    var $track2;
    var $EchoSuccess;      // if this is true, it will send the order to ECHOnline
    var $cnp_recurring;
    var $cnp_security;

    // These variables are used after a transaction has been submitted.
    // You always get back all 3 responses
    var $EchoResponse;     // All 3 ECHOTYPE responses
    var $echotype1;        // Show ECHOTYPE 1 response
    var $echotype2;        // Show ECHOTYPE 2 response - HTML format
    var $echotype3;        // Show ECHOTYPE 3 response - XML format

    // ECHOTYPE3 results - see section under ECHOTYPE3
    var $authorization;
    var $order_number;
    var $reference;
    var $status;
    var $avs_result;
    var $security_result;
    var $mac;
    var $decline_code;
    var $tran_date;
    var $merchant_name;
    var $version;

    function version_check($vercheck) {
        $minver = explode(".",$vercheck);
        $curver = explode(".",phpversion());
        for ($i=0;$i<count($minver);$i++) if ($minver[$i]<$curver[$i]) return false;
        return true;
    }

    function Submit() {
        if ($this->EchoServer) {
            $URL = $this->EchoServer;
        } else {
            $URL = "https://wwws.echo-inc.com/scripts/INR200.EXE";
        }

        $this->EchoResponse = "";

        $data = $this->getURLData();

        // get the php version number
        if (!(phpversion())) {
            die("Please email <a href=\"mailto:developer-support@echo-inc.com\">ECHO Developer Support</a> and notify them know that the echophp.class file cannot find the <a href=\"http://www.php.net\">PHP</a> version number.  Please also include your server configuration.\n<br>\n<br>\nServer Software: ".$_SERVER["SERVER_SOFTWARE"]."\n<br>\nPHP Version: ".phpversion());
        }

        // checks to see if their php is under version 4.3.  if it is, then they have to execute
        // the curl statements.

        if (!$this->version_check("4.3.0")) {
            // if the curl functions do not exist, they must install curl into php
            if (!(function_exists('curl_init'))) {
                print("Error: cURL component is missing, please install it.\n<br>\n<br>Your <a href=\"http://www.php.net\">PHP</a> currently does not have <a href=\"http://curl.haxx.se\">cURL</a> support, which is required for PHP servers older than 4.3.0.  Please contact your hosting company to resolve this issue.  <a href=\"http://curl.haxx.se\">cURL</a> must be configured with ./configure --with-ssl, and <a href=\"http://www.php.net\">PHP</a> must be configured with the --with-curl option.\n<br>\n<br>\nServer Software: ".$_SERVER["SERVER_SOFTWARE"]."\n<br>\nPHP Version: ".phpversion());
                die("");
            }

            // they have curl, but it must be configured with ssl to execute curl_exec($ch)
            else {
                $ch = @curl_init();
                curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($ch, CURLOPT_URL, $URL);
                curl_setopt ($ch, CURLOPT_POST, $data);
                curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
                if (!($this->EchoResponse = curl_exec ($ch))) {
                    print("You are receiving this error for one of the following reasons:<br><br>1) The cURL component is missing SSL support.  When installing <a href=\"http://curl.haxx.se\">cURL</a>, it must be configured with ./configure --with-ssl<br>2) The server cannot establish an internet connection to the <i>ECHO</i>nline server at " . $URL . "<br><br>Please contact your hosting company to resolve this issue.\n<br>\n<br>\nServer Software: ".$_SERVER["SERVER_SOFTWARE"]."\n<br>\nPHP Version: ".phpversion());
                    die("");
                }
                curl_close ($ch);
            }
        }

        // else their php can execute using openssl OR curl.  if openssl doesn't work, try curl.  if
        // that doesn't work, give an error message.

        else {
            // open the https:// file handle, will error out if OpenSSL support is not compiled into PHP

            ini_set('allow_url_fopen', '1');
            if (!($handle = @fopen($URL."?".$data, "r"))) {
                if ( @function_exists('curl_init') ) {
                    $ch = @curl_init();
                    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt ($ch, CURLOPT_URL, $URL);
                    curl_setopt ($ch, CURLOPT_POST, $data);
                    curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
                    if (!($this->EchoResponse = curl_exec ($ch))) {
                        print("You are receiving this error for one of the following reasons:<br><br>1) OpenSSL support is missing (needs to be configured with ./configure --with-openssl), but it found cURL instead.  However, the cURL component is missing SSL support.  When installing <a href=\"http://curl.haxx.se\">cURL</a>, it must be configured with ./configure --with-ssl<br>2) The server cannot establish an internet connection to the <i>ECHO</i>nline server at " . $URL . "<br><br>Please contact your hosting company to resolve this issue.\n<br>\n<br>\nServer Software: ".$_SERVER["SERVER_SOFTWARE"]."\n<br>\nPHP Version: ".phpversion());
                        die("");
                    }
                    curl_close ($ch);
                }
                else {
                    print("You are receiving this error for one of the following reasons:<br><br>1) OpenSSL support is missing (needs to be configured with ./configure --with-openssl).  In your phpinfo(), you are missing the section called 'OpenSSL'.  Please contact your hosting company to resolve this issue.  ");
                    if ( strcmp($_ENV["OS"],"Windows_NT") == 0 ) {
                        print("<br><br>Since this server is running under a Windows box, it may need some modifications.  In order to take advantage of the new features in PHP 4.3.0 such as SSL url wrappers you need to install PHP with built-in SSL support. In order to do so you need to install the standard <a href=\"http://www.php.net\">PHP</a> distribution and replace php4ts.dll file with one supplied in <a href=\"http://ftp.proventum.net/pub/php/win32/misc/openssl/\">this</a> archive.  ");
                        print("Since OpenSSL support is built-in into this file, please remember to comment out 'extension=php_openssl.dll' from your php.ini file since the external extension is no longer needed.");

                    }
                    else {
                        print("<a href=\"http://www.php.net\">PHP</a> needs to be configured with ./configure --with-openssl option.");
                    }
                    print("<br><br>2) The server cannot establish an internet connection to the <i>ECHO</i>nline server at " . $URL);
                    print("\n<br>\n<br>\nServer Software: ".$_SERVER["SERVER_SOFTWARE"]."\n<br>\nPHP Version: ".phpversion());
                    die("");
                }
            }
            else {
                // get the ECHO Response
                $this->EchoResponse = "";
                while (!feof ($handle)) {
                    $buffer = @fgets($handle, 4096);
                    $this->EchoResponse .= $buffer;
                }
            }
        }

        $startpos = strpos($this->EchoResponse, "<ECHOTYPE1>") + 11;
        $endpos = strpos($this->EchoResponse, "</ECHOTYPE1>");
        $this->echotype1 = substr($this->EchoResponse, $startpos, $endpos - $startpos);

        $startpos = strpos($this->EchoResponse, "<ECHOTYPE2>") + 11;
        $endpos = strpos($this->EchoResponse, "</ECHOTYPE2>");
        $this->echotype2 = substr($this->EchoResponse, $startpos, $endpos - $startpos);

        $startpos = strpos($this->EchoResponse, "<ECHOTYPE3>") + 11;
        $endpos = strpos($this->EchoResponse, "</ECHOTYPE3>");
        $this->echotype3 = substr($this->EchoResponse, $startpos, $endpos - $startpos);

        // Get all the metadata.
        $this->authorization = $this->GetEchoProp($this->echotype3, "auth_code");
        $this->order_number = $this->GetEchoProp($this->echotype3, "order_number");
        $this->reference = $this->GetEchoProp($this->echotype3, "echo_reference");
        $this->status = $this->GetEchoProp($this->echotype3, "status");
        $this->avs_result = $this->GetEchoProp($this->echotype3, "avs_result");
        $this->security_result = $this->GetEchoProp($this->echotype3, "security_result");
        $this->mac = $this->GetEchoProp($this->echotype3, "mac");
        $this->decline_code = $this->GetEchoProp($this->echotype3, "decline_code");
        $this->tran_date = $this->GetEchoProp($this->echotype3, "tran_date");
        $this->merchant_name = $this->GetEchoProp($this->echotype3, "merchant_name");
        $this->version = $this->GetEchoProp($this->echotype3, "version");

        if ($this->status == "G" or $this->status == "R") {
            if ($this->transaction_type == "AD") {
                if ($this->avs_result == "X" or $this->avs_result == "Y" or
                        $this->avs_result == "D" or $this->avs_result == "M") {
                    $this->EchoSuccess = true;
                }
                else {
                    $this->EchoSuccess = false;
                }
            }
            else $this->EchoSuccess = true;
        }
        else {
            $this->EchoSuccess = false;
        }


        if ($this->EchoResponse == "") {
            $this->EchoSuccess = False;
        }

        // make sure we assign an integer to EchoSuccess
        ($this->EchoSuccess == true) ? ($this->EchoSuccess = true) : ($this->EchoSuccess = false);

        return $this->EchoSuccess;



    } // function submit


    function getURLData() {
        $s = "";
        $s .= "order_type=" . $this->order_type;
        if ($this->transaction_type) {
            $s .= "&transaction_type=" . $this->transaction_type;
        }
        if ($this->merchant_echo_id) {
            $s .= "&merchant_echo_id=" . $this->merchant_echo_id;
        }
        if ($this->merchant_pin) {
            $s .= "&merchant_pin=" . $this->merchant_pin;
        }
        if ($this->isp_echo_id) {
            $s .= "&isp_echo_id=" . $this->isp_echo_id;
        }
        if ($this->isp_pin) {
            $s .= "&isp_pin=" . $this->isp_pin;
        }
        if ($this->authorization) {
            $s .= "&authorization=" . $this->authorization;
        }
        if ($this->billing_ip_address) {
            $s .= "&billing_ip_address=" . $this->billing_ip_address;
        }
        if ($this->billing_prefix) {
            $s .= "&billing_prefix=" . $this->billing_prefix;
        }
        if ($this->billing_name) {
            $s .= "&billing_name=" . $this->billing_name;
        }
        if ($this->billing_first_name) {
            $s .= "&billing_first_name=" . $this->billing_first_name;
        }
        if ($this->billing_last_name) {
            $s .= "&billing_last_name=" . $this->billing_last_name;
        }
        if ($this->billing_company_name) {
            $s .= "&billing_company_name=" . $billing_company_name;
        }
        if ($this->billing_address1) {
            $s .= "&billing_address1=" . $this->billing_address1;
        }
        if ($this->billing_address2) {
            $s .= "&billing_address2=" . $this->billing_address2;
        }
        if ($this->billing_city) {
            $s .= "&billing_city=" . $this->billing_city;
        }
        if ($this->billing_state) {
            $s .= "&billing_state=" . $this->billing_state;
        }
        if ($this->billing_zip) {
            $s .= "&billing_zip=" . $this->billing_zip;
        }
        if ($this->billing_country) {
            $s .= "&billing_country=" . $this->billing_country;
        }
        if ($this->billing_phone) {
            $s .= "&billing_phone=" . $this->billing_phone;
        }
        if ($this->billing_fax) {
            $s .= "&billing_fax=" . $this->billing_fax;
        }
        if ($this->billing_email) {
            $s .= "&billing_email=" . $this->billing_email;
        }
        if ($this->cc_number) {
            $s .= "&cc_number=" . $this->cc_number;
        }
        if ($this->ccexp_month) {
            $s .= "&ccexp_month=" . $this->ccexp_month;
        }
        if ($this->ccexp_year) {
            $s .= "&ccexp_year=" . $this->ccexp_year;
        }
        if ($this->counter) {
            $s .= "&counter=" . $this->counter;
        }
        if ($this->debug) {
            $s .= "&debug=" . $this->debug;
        }

        if ($this->ec_account) {
            $s .= "&ec_account=" . $this->ec_account;
        }
        if ($this->ec_account_type) {
            $s .= "&ec_account_type=" . $this->ec_account_type;
        }
        if ($this->ec_payment_type) {
            $s .= "&ec_payment_type=" . $this->ec_payment_type;
        }
        if ($this->ec_address1) {
            $s .= "&ec_address1=" . $this->ec_address1;
        }
        if ($this->ec_address2) {
            $s .= "&ec_address2=" . $this->ec_address2;
        }
        if ($this->ec_bank_name) {
            $s .= "&ec_bank_name=" . $this->ec_bank_name;
        }
        if ($this->ec_city) {
            $s .= "&ec_city=" . $this->ec_city;
        }
        if ($this->ec_state) {
            $s .= "&ec_state=" . $this->ec_state;
        }
        if ($this->ec_email) {
            $s .= "&ec_email=" . $this->ec_email;
        }
        if ($this->ec_first_name) {
            $s .= "&ec_first_name=" . $this->ec_first_name;
        }
        if ($this->ec_last_name) {
            $s .= "&ec_last_name=" . $this->ec_last_name;
        }
        if ($this->ec_other_name) {
            $s .= "&ec_other_name=" . $this->ec_other_name;
        }
        if ($this->ec_id_country) {
            $s .= "&ec_id_country=" . $this->ec_id_country;
        }
        if ($this->ec_id_exp_mm) {
            $s .= "&ec_id_exp_mm=" . $this->ec_id_exp_mm;
        }
        if ($this->ec_id_exp_dd) {
            $s .= "&ec_id_exp_dd=" . $this->ec_id_exp_dd;
        }
        if ($this->ec_id_exp_yy) {
            $s .= "&ec_id_exp_yy=" . $this->ec_id_exp_yy;
        }
        if ($this->ec_id_exp_yy) {
            $s .= "&ec_id_exp_yy=" . $this->ec_id_exp_yy;
        }
        if ($this->ec_id_number) {
            $s .= "&ec_id_number=" . $this->ec_id_number;
        }
        if ($this->ec_id_state) {
            $s .= "&ec_id_state=" . $this->ec_id_state;
        }
        if ($this->ec_id_type) {
            $s .= "&ec_id_type=" . $this->ec_id_type;
        }
        if ($this->ec_payee) {
            $s .= "&ec_payee=" . $this->ec_payee;
        }
        if ($this->ec_rt) {
            $s .= "&ec_rt=" . $this->ec_rt;
        }
        if ($this->ec_serial_number) {
            $s .= "&ec_serial_number=" . $this->ec_serial_number;
        }
        if ($this->ec_transaction_dt) {
            $s .= "&ec_transaction_dt=" . $this->ec_transaction_dt;
        }
        if ($this->ec_zip) {
            $s .= "&ec_zip=" . $this->ec_zip;
        }

        if ($this->grand_total) {
            $s .= "&grand_total=" . $this->grand_total;
        }
        if ($this->merchant_email) {
            $s .= "&merchant_email=" . $this->merchant_email;
        }
        if ($this->merchant_trace_nbr) {
            $s .= "&merchant_trace_nbr=" . $this->merchant_trace_nbr;
        }
        if ($this->original_amount) {
            $s .= "&original_amount=" . $this->original_amount;
        }
        if ($this->original_trandate_mm) {
            $s .= "&original_trandate_mm=" . $this->original_trandate_mm;
        }
        if ($this->original_trandate_dd) {
            $s .= "&original_trandate_dd=" . $this->original_trandate_dd;
        }
        if ($this->original_trandate_yyyy) {
            $s .= "&original_trandate_yyyy=" . $this->original_trandate_yyyy;
        }
        if ($this->original_reference) {
            $s .= "&original_reference=" . $this->original_reference;
        }
        if ($this->order_number) {
            $s .= "&order_number=" . $this->order_number;
        }
        if ($this->product_description) {
            $s .= "&product_description=" . $this->product_description;
        }
        if ($this->purchase_order_number) {
            $s .= "&purchase_order_number=" . $this->purchase_order_number;
        }
        if ($this->sales_tax) {
            $s .= "&sales_tax=" . $this->sales_tax;
        }
        if ($this->track1) {
            $s .= "&track1=" . $this->track1;
        }
        if ($this->track2) {
            $s .= "&track2=" . $this->track2;
        }
        if ($this->cnp_security) {
            $s .= "&cnp_security=" . $this->cnp_security;
        }
        if ($this->cnp_recurring) {
            $s .= "&cnp_recurring=" . $this->cnp_recurring;
        }

        return $s;

    } // end getURLData



    /**********************************************
		All the get/set methods for the echo properties
		***********************************************/
    function set_order_type($value) {
        $this->order_type = urlencode($value);
    }

    function get_order_type() {
        return $this->order_type;
    }

    function set_transaction_type($value) {
        $this->transaction_type = urlencode($value);
    }

    function get_transaction_type() {
        return $this->transaction_type;
    }

    function set_merchant_echo_id($value) {
        $this->merchant_echo_id = urlencode($value);
    }

    function get_merchant_echo_id() {
        return $this->merchant_echo_id;
    }

    function set_merchant_pin($value) {
        $this->merchant_pin = urlencode($value);
    }

    function get_merchant_pin() {
        return $this->merchant_pin;
    }

    function set_isp_echo_id($value) {
        $this->isp_echo_id = urlencode($value);
    }

    function get_isp_echo_id() {
        return $this->isp_echo_id;
    }

    function set_isp_pin($value) {
        $this->isp_pin = urlencode($value);
    }

    function get_isp_pin() {
        return $this->isp_pin;
    }

    function set_authorization($value) {
        $this->authorization = urlencode($value);
    }

    function get_authorization() {
        return $this->authorization;
    }

    function set_billing_ip_address($value) {
        $this->billing_ip_address = urlencode($value);
    }

    function get_billing_ip_address() {
        return $this->billing_ip_address;
    }

    function set_billing_prefix($value) {
        $this->billing_prefix = urlencode($value);
    }

    function get_billing_prefix() {
        return $this->billing_prefix;
    }

    function set_billing_name($value) {
        $this->billing_name = urlencode($value);
    }

    function get_billing_name() {
        return $this->billing_name;
    }

    function set_billing_first_name($value) {
        $this->billing_first_name = urlencode($value);
    }

    function get_billing_first_name() {
        return $this->billing_first_name;
    }
    function set_billing_last_name($value) {
        $this->billing_last_name = urlencode($value);
    }

    function get_billing_last_name() {
        return $this->billing_last_name;
    }
    function set_billing_company_name($value) {
        $this->billing_company_name = urlencode($value);
    }

    function get_billing_company_name() {
        return $this->billing_company_name;
    }

    function set_billing_address1($value) {
        $this->billing_address1 = urlencode($value);
    }

    function get_billing_address1() {
        return $this->billing_address1;
    }

    function set_billing_address2($value) {
        $this->billing_address2 = urlencode($value);
    }

    function get_billing_address2() {
        return $this->billing_address2;
    }

    function set_billing_city($value) {
        $this->billing_city = urlencode($value);
    }

    function get_billing_city() {
        return $this->billing_city;
    }

    function set_billing_state($value) {
        $this->billing_state = urlencode($value);
    }

    function get_billing_state() {
        return $this->billing_state;
    }

    function set_billing_zip($value) {
        $this->billing_zip = urlencode($value);
    }

    function get_billing_zip() {
        return $this->billing_zip;
    }

    function set_billing_country($value) {
        $this->billing_country = urlencode($value);
    }

    function get_billing_country() {
        return $this->billing_country;
    }

    function set_billing_phone($value) {
        $this->billing_phone = urlencode($value);
    }

    function get_billing_phone() {
        return $this->billing_phone;
    }

    function set_billing_fax($value) {
        $this->billing_fax = urlencode($value);
    }

    function get_billing_fax() {
        return $this->billing_fax;
    }

    function set_billing_email($value) {
        $this->billing_email = urlencode($value);
    }

    function get_billing_email() {
        return $this->billing_email;
    }

    function set_cc_number($value) {
        $this->cc_number = urlencode($value);
    }

    function get_cc_number() {
        return $this->cc_number;
    }

    function set_ccexp_month($value) {
        $this->ccexp_month = urlencode($value);
    }

    function get_ccexp_month() {
        return $this->ccexp_month;
    }

    function set_ccexp_year($value) {
        $this->ccexp_year = urlencode($value);
    }

    function get_ccexp_year() {
        return $this->ccexp_year;
    }

    function set_counter($value) {
        $this->counter = urlencode($value);
    }

    function get_counter() {
        return $this->counter;
    }

    function set_debug($value) {
        $this->debug = urlencode($value);
    }

    function get_debug() {
        return $this->debug;
    }

    function set_ec_account($value) {
        $this->ec_account = urlencode($value);
    }

    function get_ec_account() {
        return $this->ec_account;
    }

    function set_ec_account_type($value) {
        $this->ec_account_type = urlencode($value);
    }

    function get_ec_account_type() {
        return $this->ec_account_type;
    }

    function set_ec_payment_type($value) {
        $this->ec_payment_type = urlencode($value);
    }

    function get_ec_payment_type() {
        return $this->ec_payment_type;
    }

    function set_ec_address1($value) {
        $this->ec_address1 = urlencode($value);
    }

    function get_ec_address1() {
        return $this->ec_address1;
    }

    function set_ec_address2($value) {
        $this->ec_address2 = urlencode($value);
    }

    function get_ec_address2() {
        return $this->ec_address2;
    }

    function set_ec_bank_name($value) {
        $this->ec_bank_name = urlencode($value);
    }

    function get_ec_bank_name() {
        return $this->ec_bank_name;
    }

    function set_ec_city($value) {
        $this->ec_city = urlencode($value);
    }

    function get_ec_city() {
        return $this->ec_city;
    }

    function set_ec_email($value) {
        $this->ec_email = urlencode($value);

    }

    function get_ec_email() {
        return $this->ec_email;
    }

    function set_ec_first_name($value) {
        $this->ec_first_name = urlencode($value);
    }

    function get_ec_first_name() {
        return $this->ec_first_name;
    }

    function set_ec_id_country($value) {
        $this->ec_id_country = urlencode($value);
    }

    function get_ec_id_country() {
        return $this->ec_id_country;
    }

    function set_ec_id_exp_mm($value) {
        $this->ec_id_exp_mm = urlencode($value);
    }

    function get_ec_id_exp_mm() {
        return $this->ec_id_exp_mm;
    }

    function set_ec_id_exp_dd($value) {
        $this->ec_id_exp_dd = urlencode($value);
    }

    function get_ec_id_exp_dd() {
        return $this->ec_id_exp_dd;
    }

    function set_ec_id_exp_yy($value) {
        $this->ec_id_exp_yy = urlencode($value);
    }

    function get_ec_id_exp_yy() {
        return $this->ec_id_exp_yy;
    }

    function set_ec_id_number($value) {
        $this->ec_id_number = urlencode($value);
    }

    function get_ec_id_number() {
        return $this->ec_id_number;
    }

    function set_ec_id_state($value) {
        $this->ec_id_state = urlencode($value);
    }

    function get_ec_id_state() {
        return $this->ec_id_state;
    }

    function set_ec_id_type($value) {
        $this->ec_id_type = urlencode($value);
    }

    function get_ec_id_type() {
        return $this->ec_id_type;
    }

    function set_ec_last_name($value) {
        $this->ec_last_name = urlencode($value);
    }

    function get_ec_last_name() {
        return $this->ec_last_name;
    }

    function set_ec_other_name($value) {
        $this->ec_other_name = urlencode($value);
    }

    function get_ec_other_name() {
        return $this->ec_other_name;
    }

    function set_ec_payee($value) {
        $this->ec_payee = urlencode($value);
    }

    function get_ec_payee() {
        return $this->ec_payee;
    }

    function set_ec_rt($value) {
        $this->ec_rt = urlencode($value);
    }

    function get_ec_rt() {
        return $this->ec_rt;
    }

    function set_ec_serial_number($value) {
        $this->ec_serial_number = urlencode($value);
    }

    function get_ec_serial_number() {
        return $this->ec_serial_number;
    }

    function set_ec_state($value) {
        $this->ec_state = urlencode($value);
    }

    function get_ec_state() {
        return $this->ec_state;
    }

    function set_ec_transaction_dt($value) {
        $this->ec_transaction_dt = urlencode($value);
    }

    function get_ec_transaction_dt() {
        return $this->ec_transaction_dt;
    }


    function set_ec_zip($value) {
        $this->ec_zip = urlencode($value);
    }

    function get_ec_zip() {
        return $this->ec_zip;
    }

    function set_grand_total($value) {
        $this->grand_total = sprintf("%01.2f", $value);
    }

    function get_grand_total() {
        return $this->grand_total;
    }

    function set_merchant_email($value) {
        $this->merchant_email = urlencode($value);
    }

    function get_merchant_email() {
        return $this->merchant_email;
    }

    function set_merchant_trace_nbr($value) {
        $this->merchant_trace_nbr = urlencode($value);
    }

    function get_merchant_trace_nbr() {
        return $this->merchant_trace_nbr;
    }

    function set_original_amount($value) {
        $this->original_amount = sprintf("%01.2f", $value);
    }

    function get_original_amount() {
        return $this->original_amount;
    }

    function set_original_trandate_mm($value) {
        $this->original_trandate_mm = urlencode($value);
    }

    function get_original_trandate_mm() {
        return $this->original_trandate_mm;
    }

    function set_original_trandate_dd($value) {
        $this->original_trandate_dd = urlencode($value);
    }

    function get_original_trandate_dd() {
        return $this->original_trandate_dd;
    }

    function set_original_trandate_yyyy($value) {
        $this->original_trandate_yyyy = urlencode($value);
    }

    function get_original_trandate_yyyy() {
        return $this->original_trandate_yyyy;
    }

    function set_original_reference($value) {
        $this->original_reference = urlencode($value);
    }

    function get_original_reference() {
        return $this->original_reference;
    }

    function set_order_number($value) {
        $this->order_number = urlencode($value);
    }

    function get_order_number() {
        return $this->order_number;
    }

    function set_product_description($value) {
        $this->product_description = urlencode($value);
    }

    function get_product_description() {
        return $this->product_description;
    }

    function set_purchase_order_number($value) {
        $this->purchase_order_number = urlencode($value);
    }

    function get_purchase_order_number() {
        return $this->purchase_order_number;
    }

    function set_sales_tax($value) {
        $this->sales_tax = urlencode($value);
    }

    function get_sales_tax() {
        return $this->sales_tax;
    }

    function set_track1($value) {
        $this->track1 = urlencode($value);
    }

    function get_track1() {
        return $this->track1;
    }

    function set_track2($value) {
        $this->track2 = urlencode($value);
    }

    function get_track2() {
        return $this->track2;
    }

    function set_cnp_recurring($value) {
        $this->cnp_recurring = urlencode($value);
    }

    function set_cnp_security($value) {
        $this->cnp_security = urlencode($value);
    }


    /************************************************
						Helper functions
		************************************************/

    function get_version() {
        return "OpenECHO.com PHP module 1.7.1 06/24/2004";
    }

    function getRandomCounter() {
        mt_srand ((double) microtime() * 1000000);
        return mt_rand();
    }

    function get_EchoResponse() {
        return $this->EchoResponse;
    }

    function get_echotype1() {
        return $this->echotype1;
    }

    function get_echotype2() {
        return $this->echotype2;
    }

    function get_echotype3() {
        return $this->echotype3;
    }

    function set_EchoServer($value) {
        $this->EchoServer = $value;
    }

    function get_avs_result() {
        return $this->avs_result;
    }

    function get_reference() {
        return $this->reference;
    }

    function get_EchoSuccess() {
        return $this->EchoSuccess;
    }

    function get_status() {
        return $this->status;
    }

    function get_security_result() {
        return $this->GetEchoProp($this->echotype3, "security_result");
    }

    function get_mac() {
        return $this->GetEchoProp($this->echotype3, "mac");
    }

    function get_decline_code() {
        return $this->GetEchoProp($this->echotype3, "decline_code");
    }

    function GetEchoProp($haystack, $prop) {
        if (($start_pos = strpos(strtolower($haystack), "<$prop>"))!==false) {
            $start_pos += strlen("<$prop>");
            $end_pos = strpos(strtolower($haystack), "</$prop", $start_pos);
            return substr($haystack, $start_pos, $end_pos - $start_pos);
        } else return "";
    }

} // end of class
?>
