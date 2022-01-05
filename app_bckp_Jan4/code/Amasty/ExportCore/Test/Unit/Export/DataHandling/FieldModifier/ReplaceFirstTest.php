<?php
declare(strict_types=1);

namespace Amasty\ExportCore\Test\Unit\Export\DataHandling\FieldModifier;

use Amasty\ExportCore\Export\DataHandling\FieldModifier\ReplaceFirst;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ExportCore\Export\DataHandling\FieldModifier\ReplaceFirst
 */
class ReplaceFirstTest extends TestCase
{
    /**
     * Data provider for transform
     * @return array
     */
    public function validateDataProvider(): array
    {
        return [
            'default' => [
                ['from_input_value' => 'from_value', 'to_input_value' => 'to_value'],
                'from_value testtext from_value',
                'to_value testtext from_value'
            ],
            'empty' => [
                ['from_input_value' => 'from_value', 'to_input_value' => ''],
                'from_value testtext from_value',
                ' testtext from_value'
            ],
            'to_int' => [
                ['from_input_value' => 'from_value', 'to_input_value' => 123],
                'from_value testtext from_value',
                '123 testtext from_value'
            ],
            'from_to_int' => [
                ['from_input_value' => 123, 'to_input_value' => 123],
                'from_value testtext from_value',
                'from_value testtext from_value'
            ],
            'empty_value' => [
                ['from_input_value' => 'from_value', 'to_input_value' => 'to_value'],
                '',
                ''
            ],
            'without_key' => [
                ['test_key' => 'test_value'],
                '',
                ''
            ]
        ];
    }

    /**
     * @param array $config
     * @param string $value
     * @param string $expectedResult
     * @dataProvider validateDataProvider
     */
    public function testTransform(array $config, string $value, string $expectedResult)
    {
        $objectManager = new ObjectManager($this);
        $modifier = $objectManager->getObject(ReplaceFirst::class, ['config' => $config]);
        $this->assertSame($expectedResult, $modifier->transform($value));
    }
}
