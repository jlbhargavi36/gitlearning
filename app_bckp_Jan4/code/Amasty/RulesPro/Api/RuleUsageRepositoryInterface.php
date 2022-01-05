<?php

namespace Amasty\RulesPro\Api;

interface RuleUsageRepositoryInterface
{
    const COUNT_USAGE_COLUMN = 'count';

    const LIMIT_USAGE_COLUMN = 'limit';

    /**
     * @param int $salesRuleId
     *
     * @return int
     */
    public function getUsageLimitByRuleId(int $salesRuleId): int;

    /**
     * @return void
     */
    public function updateUsageRulesLimit(): void;

    /**
     * @return void
     */
    public function updateUsageRulesCount(): void;

    /**
     * @param int $salesRuleId
     *
     * @return int
     */
    public function getUsageCountByRuleId(int $salesRuleId): int;

    /**
     * @param int $salesRuleId
     * @param int $limit
     */
    public function saveUsageLimit(int $salesRuleId, int $limit);

    /**
     * @param int $salesRuleId
     * @param int $count
     */
    public function saveUsageCount(int $salesRuleId, int $count);

    /**
     * @param array $ruleIds
     */
    public function incrementUsageCountByRuleIds(array $ruleIds);
}
