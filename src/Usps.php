<?php

/**
 * Available Laravel Methods
 * Add other USPS API Methods
 * Based on Vincent Gabriel @VinceG USPS PHP-Api https://github.com/VinceG/USPS-php-api
 *
 * @since  1.0
 * @author John Paul Medina
 * @author Vincent Gabriel
 */

// namespace Usps;
namespace Simplexi\Greetr;

function __autoload($class_name) {
    include $class_name . '.php';
}

class Usps {

    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function validate($street, $zip, $apartment = false, $city = false, $state = false) {
        $verify = new AddressVerify($this->config['username']);
        $address = new Address;
        $address->setFirmName(null);
        $address->setApt($apartment);
        $address->setAddress($street);
        $address->setCity($city);
        $address->setState($state);
        $address->setZip5($zip);
        $address->setZip4('');

        // Add the address object to the address verify class
        $verify->addAddress($address);

        // Perform the request and return result
        $val1 = $verify->verify();
        $val2 = $verify->getArrayResponse();

        // var_dump($verify->isError());

        // See if it was successful
        if ($verify->isSuccess()) {
            return ['address' => $val2['AddressValidateResponse']['Address']];
        } else {
            return ['error' => $verify->getErrorMessage()];
        }      
    }

    public function priority() {
        
        $label = new PriorityLabel($this->config['username']);
        // During test mode this seems not to always work as expected
        $label->setTestMode(true);

        $label->setFromAddress('test', 'Doe', '', '5161 Lankershim Blvd', 'North Hollywood', 'CA', '91601', '# 204', '', '8882721214');
        $label->setToAddress('Vincent', 'Gabriel', '', '230 Murray St', 'New York', 'NY', '10282');
        $label->setWeightOunces(1);
        $label->setField(36, 'LabelDate', '03/12/2014');

        //$label->setField(32, 'SeparateReceiptPage', 'true');

        // Perform the request and return result
        $label->createLabel();

        //print_r($label->getArrayResponse());
        //print_r($label->getPostData());
        //var_dump($label->isError());

        // See if it was successful
        if ($label->isSuccess()) {
            //echo 'Done';
            //echo "\n Confirmation:" . $label->getConfirmationNumber();

            $label = $label->getLabelContents();

            if ($label) {
                $contents = base64_decode($label);
                return ['contents' => $contents];
            }
        } else {
            echo 'Error: ' . $label->getErrorMessage();
        }

    }
}
