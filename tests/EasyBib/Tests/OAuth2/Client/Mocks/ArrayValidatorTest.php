<?php

namespace EasyBib\Tests\Api\Client\Mocks\Session;

use EasyBib\OAuth2\Client\ArrayValidationException;
use EasyBib\OAuth2\Client\ArrayValidator;

class ArrayValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getValidData()
    {
        return [
            [
                [
                    'foo' => 'foo123',
                    'bar' => 'bar123',
                    'baz' => 'baz123',
                ],
                [
                    'foo',
                    'bar',
                    'baz',
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function getWithMissingData()
    {
        $invalidData = [];

        foreach (array_keys($this->getValidData()[0][0]) as $key) {
            $data = $this->getValidData()[0];
            unset($data[0][$key]);
            $invalidData[] = [$data[0], $data[1], $key];
        }

        return $invalidData;
    }

    /**
     * @return array
     */
    public function getWithExtraData()
    {
        $invalidData = [];

        $data = $this->getValidData()[0];
        $data[0]['jamma'] = 'bamma';
        $invalidData[] = [$data[0], $data[1], 'jamma'];

        return $invalidData;
    }

    /**
     * @dataProvider getWithMissingData
     * @params array $input
     * @params array $requiredKeys
     * @params string $expectedMissingKey
     */
    public function testValidateWithMissingData(array $input, array $requiredKeys, $expectedMissingKey)
    {
        $this->setExpectedException(
            ArrayValidationException::class,
            'Missing key(s) ' . $expectedMissingKey
        );

        $validator = new ArrayValidator($requiredKeys);
        $validator->validate($input);
    }

    /**
     * @dataProvider getWithExtraData
     * @params array $input
     * @params array $expectedKeys
     * @params string $expectedMissingKey
     */
    public function testValidateWithExtraData(array $input, array $expectedKeys, $expectedExtraKey)
    {
        $this->setExpectedException(
            ArrayValidationException::class,
            'Unexpected key(s) ' . $expectedExtraKey
        );

        $validator = new ArrayValidator($expectedKeys, $expectedKeys);
        $validator->validate($input);
    }

    /**
     * @dataProvider getWithExtraData
     * @params array $input
     * @params array $expectedKeys
     * @params string $expectedMissingKey
     */
    public function testValidateWithExtraDataNoPermittedKeysGiven(array $input, array $expectedKeys, $expectedExtraKey)
    {
        $validator = new ArrayValidator($expectedKeys);
        $validator->validate($input);
    }

    /**
     * @dataProvider getValidData
     * @params array $input
     * @params array $requiredKeys
     */
    public function testValidateWithValidData(array $input, array $requiredKeys)
    {
        $validator = new ArrayValidator($requiredKeys);
        $validator->validate($input);
    }
}
