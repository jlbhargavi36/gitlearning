<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Test\Unit\Model\Amount;

use Amasty\StoreCreditProduct\Model\Amount\AmountFilter;
use PHPUnit\Framework\TestCase;

class AmountFilterTest extends TestCase
{
    /**
     * @var AmountFilter
     */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new AmountFilter();
    }

    /**
     * @param array $input
     * @param array $result
     * @dataProvider amountsDataProvider
     */
    public function testFilterByWebsite(array $input, array $result)
    {
        $this->assertEquals($result, $this->subject->filterByWebsite($input, '1'));
    }

    /**
     * @return array
     */
    public function amountsDataProvider(): array
    {
        return [
            [[], []],
            [[['website_id' => '1'], ['website_id' => '2']], [['website_id' => '1']]],
            [[['website_id' => '1'], ['website_id' => '0']], [['website_id' => '1'], ['website_id' => '0']]],
        ];
    }
}
