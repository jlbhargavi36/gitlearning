<?php

namespace Amasty\ExportCore\Export\Config;

use Magento\Framework\Module\Manager;

class IsModuleDisabled
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Manager $moduleManager,
        array $config = []
    ) {
        $this->moduleName = $config['moduleName'] ?? '';
        $this->moduleManager = $moduleManager;
    }

    public function isEnabled(): bool
    {
        return (!empty($this->moduleName) && !$this->moduleManager->isEnabled($this->moduleName));
    }
}
