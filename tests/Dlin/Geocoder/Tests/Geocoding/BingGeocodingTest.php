<?php

namespace Dlin\Geocoder\Tests\Geocoding;
use Dlin\Geocoder\Geocoding\BingGeocoding;

/**
 * 
 * User: davidlin
 * Date: 11/09/13
 * Time: 11:13 PM
 * 
 */

class BingGeocodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Dlin\Geocoder\Geocoding\BingGeocoding $geoCoding
     */
    protected $geoCoding;

    public function setUp()
    {
        //$bingMapsKey = 'ApUmGCPD3VPcMRjlZUjVz1Z5uPHhlZYqA6Up9wvOVjQrYmJlygS3ftM87SHlIyx9';
        $bingMapsKey = 'AvwNOuwxCZESwB9_p_RAHncR-oypS6UTsX5_g9u4Ejyt32G59_kKnvTSG3ySE3Q8';
        $this->geoCoding = new BingGeocoding($bingMapsKey);
    }

    public function testForwardReverse(){

        $address = $this->geoCoding->forward("5/48 Pirrama Rd, Pyrmont 2009 NSW, Australia");

        $this->assertEquals('48 Pirrama Rd', $address->addressLine1);
        $this->assertEquals('Pyrmont', $address->suburb);
        $this->assertEquals('NSW', $address->state);
        $this->assertEquals('Australia', $address->country);
        $this->assertEquals('2009', $address->postcode);
        $this->assertFalse( $address->partial);
        $this->assertEquals('48 Pirrama Rd, Pyrmont, NSW 2009, Australia', $address->formattedAddress);
        $this->assertEquals('-33.8666344', $address->latitude);
        $this->assertEquals('151.19528048', $address->longitude);

        $address = $this->geoCoding->reverse("-33.8666344", "151.19528048");

        $this->assertEquals('48 Pirrama Rd', $address->addressLine1);
        $this->assertEquals('Pyrmont', $address->suburb);
        $this->assertEquals('NSW', $address->state);
        $this->assertEquals('Australia', $address->country);
        $this->assertEquals('2009', $address->postcode);
        $this->assertTrue($address->partial);
        $this->assertEquals('48 Pirrama Rd, Pyrmont, NSW 2009, Australia', $address->formattedAddress);


    }


}