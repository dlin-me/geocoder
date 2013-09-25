<?php
/**
 *
 * User: davidlin
 * Date: 12/09/13
 * Time: 11:15 PM
 *
 */
namespace Dlin\Geocoder\Tests;

use Dlin\Geocoder\Geocoder;

class GeocoderTest extends \PHPUnit_Framework_TestCase
{

    public function testGeocoder()
    {
        $coder = new Geocoder(__DIR__."/config.ini");
        $address = $coder->reverse("-33.86687", "151.19565");



        print_r($address);

/**
        $this->assertEquals('Provider4',$address->geoCoding);

        $address = $coder->forward("1 Queen Street, Melbourne, Vic, au");

        $this->assertEquals('Provider3',$address->geoCoding);

        $address = $coder->reverse("-33.86687", "151.19565");

        $this->assertEquals('Provider2',$address->geoCoding);
**/
    }
}