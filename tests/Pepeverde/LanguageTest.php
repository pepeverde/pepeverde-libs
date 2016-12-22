<?php

namespace Pepeverde\Test;

use Pepeverde\Language;

class LanguageTest extends \PHPUnit_Framework_TestCase
{
    /** @var Language */
    private $Language;

    protected function setUp()
    {
        parent::setUp();
        $this->Language = new Language();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Language);
    }

    /**
     * @dataProvider languageProvider
     */
    public function testInfo($languageCode, $expectedName)
    {
        $this->assertEquals($expectedName, $this->Language->info($languageCode)['name']);
    }

    public function testInfoNotExists()
    {
        $this->assertFalse($this->Language->info('tlh_001'));
    }

    public function testAll()
    {
        $languages = array(
            'it_IT' => array(
                'name' => 'Italiano'
            ),
            'en_US' => array(
                'name' => 'Inglese'
            ),
            'de_DE' => array(
                'name' => 'Tedesco'
            ),
        );

        $this->assertEquals($languages, $this->Language->all());
    }

    public function languageProvider()
    {
        return array(
            array('it_IT', 'Italiano'),
            array('en_US', 'Inglese'),
            array('de_DE', 'Tedesco'),
        );
    }

}