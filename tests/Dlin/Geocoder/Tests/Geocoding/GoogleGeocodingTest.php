<?php

namespace Dlin\Geocoder\Tests\Geocoding;
use Dlin\Geocoder\Geocoding\GoogleGeocoding;

/**
 *
 * User: davidlin
 * Date: 11/09/13
 * Time: 11:13 PM
 *
 */

class GoogleGeocodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Dlin\Geocoder\Geocoding\GoogleGeocoding $geoCoding
     */
    protected $geoCoding;

    public function setUp()
    {
        $this->geoCoding = new GoogleGeocoding();
    }

    public function testForwardReverse(){

        $address = $this->geoCoding->forward("5/48 Pirrama Rd, Pyrmont 2009 NSW, Australia");

        $this->assertEquals('48 Pirrama Rd', $address->addressLine1);
        $this->assertEquals('Pyrmont', $address->suburb);
        $this->assertEquals('NSW', $address->state);
        $this->assertEquals('Australia', $address->country);
        $this->assertEquals('2009', $address->postcode);
        $this->assertFalse($address->partial);
        $this->assertEquals('5/48 Pirrama Road, Pyrmont NSW 2009, Australia', $address->formattedAddress);
        $this->assertEquals('-33.86687', $address->latitude);
        $this->assertEquals('151.19565', $address->longitude);

        $address = $this->geoCoding->reverse("-33.86687", "151.19565");

        $this->assertEquals('48 Pirrama Rd', $address->addressLine1);
        $this->assertEquals('Pyrmont', $address->suburb);
        $this->assertEquals('NSW', $address->state);
        $this->assertEquals('Australia', $address->country);
        $this->assertEquals('2009', $address->postcode);
        $this->assertFalse($address->partial);
        $this->assertEquals('5/48 Pirrama Road, Pyrmont NSW 2009, Australia', $address->formattedAddress);

        $address = $this->geoCoding->forward('7489, Australia');
        $this->assertNotEquals('Australia', $address->country);
        $address = $this->geoCoding->forward('7489, Australia', 'AU');
        $this->assertEquals('Australia', $address->country);

    }


}