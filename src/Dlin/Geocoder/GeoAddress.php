<?php
/**
 * 
 * User: davidlin
 * Date: 11/09/13
 * Time: 10:43 PM
 * 
 */

namespace Dlin\Geocoder;


class GeoAddress {
    public $addressLine1;
    public $addressLine2;
    public $suburb;
    public $state;
    public $postcode;
    public $country;
    public $latitude;
    public $longitude;
    public $partial; //if the address is not complete/accurate
    public $formattedAddress;
    public $geoCoding;
}