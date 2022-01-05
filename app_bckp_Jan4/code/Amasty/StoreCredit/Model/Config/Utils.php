<?php

declare(strict_types=1);

namespace Amasty\StoreCredit\Model\Config;

class Utils
{
    /**
     * Convert comma separated string to array.
     *
     * @param string $string
     * @return array
     */
    public function convertToArray(string $string): array
    {
        $result = explode(',', $string);
        $result = array_map(function (string $value) {
            return trim($value);
        }, $result);
        $result = array_filter($result);

        return $result;
    }
}
