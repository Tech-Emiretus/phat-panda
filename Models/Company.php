<?php

namespace App\Models;

use Exception;

class Company extends Model
{
    /**
     * API endpoint for the model's datasource.
     *
     * @static
     */
    protected const API_ENDPOINT = 'https://5f27781bf5d27e001612e057.mockapi.io/webprovise/companies';

    /**
     * Total travel cost per company
     * In format [ {company_id} => {total_cost} ]
     *
     * @var array
     */
    protected array $travel_costs = [];

    /**
     * Build the nested company data. We can fetch
     * the built tree with/without the associated traveling cost.
     *
     * @param bool $associate_costs
     * @throws \Exception
     * @return array
     */
    public function buildNestedCompanyData(bool $associate_costs = true): array
    {
        if ($associate_costs && empty($this->travel_costs)) {
            throw new Exception('Travel cost per company is not setup.');
        }

        $parentNodes = $this->getParentCompanies();

        foreach ($parentNodes as &$parent) {
            $children = $this->getChildCompanies($parent['id'], $associate_costs);

            if ($associate_costs) {
                $cost = (float) $this->travel_costs[$parent['id']];
                $parent['cost'] = $cost + (float) array_sum(array_column($children, 'cost'));
            }

            $parent['children'] = $children;
        }

        return array_values($parentNodes);
    }

    /**
     * Sets the travel cost on this company instance.
     *
     * @param array $costs
     * @return \App\Models\Company
     */
    public function setTravelCosts(array $costs): self
    {
        $this->travel_costs = $costs;

        return $this;
    }

    /**
     * Get a list of parent companies. Meaning companies
     * which are not nested under other companies.
     *
     * @return array
     */
    protected function getParentCompanies(): array
    {
        return array_filter(
            $this->data,
            fn ($company, $key) => $company['parentId'] === '0',
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * For the specified company id, get its child companies.
     * This function is called recursively until the last nested child
     * of the specified company is found. We can build this tree with/without
     * the associated traveling cost per child.
     *
     * @param string $company_id
     * @param bool $associate_costs
     * @return array
     */
    protected function getChildCompanies(string $company_id, bool $associate_costs = true): array
    {
        $children = array_filter(
            $this->data,
            fn ($company, $key) => $company['parentId'] === $company_id,
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($children as &$child) {
            $nested_children = $this->getChildCompanies($child['id']);

            if ($associate_costs) {
                $cost = (float) $this->travel_costs[$child['id']];
                $child['cost'] = $cost + (float) array_sum(array_column($nested_children, 'cost'));
            }

            $child['children'] = $nested_children;
        }

        return array_values($children);
    }
}
