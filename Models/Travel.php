<?php

namespace App\Models;

class Travel extends Model
{
    /**
     * API endpoint for the model's datasource.
     *
     * @static
     */
    protected const API_ENDPOINT = 'https://5f27781bf5d27e001612e057.mockapi.io/webprovise/travels';

    /**
     * Get a new Travel instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->sanitizeData();
    }

    /**
     * Get a flattened array of just the total travel cost
     * per company.
     *
     * @return array
     */
    public function getTotalTravelCostPerCompany(): array
    {
        $results = [];
        $grouped_data = $this->getTravelListGroupedByCompany();

        foreach ($grouped_data as $company_id => $data) {
            $results[$company_id] = (float) array_sum(array_column($data, 'price'));
        }

        return $results;
    }

    /**
     * Get the individual travel records grouped by their company.
     *
     * @return array
     */
    public function getTravelListGroupedByCompany(): array
    {
        $grouped_data = [];

        foreach ($this->data as $data) {
            if (!array_key_exists($data['companyId'], $grouped_data)) {
                $grouped_data[$data['companyId']] = [];
            }

            $grouped_data[$data['companyId']][] = $data;
        }

        return $grouped_data;
    }

    /**
     * Sanitize the data loaded from the api.
     * In particular, price should be a float instead of a string.
     *
     * @return void
     */
    private function sanitizeData(): void
    {
        $this->data = array_map(function ($data) {
            $data['price'] = (float) $data['price'];

            return $data;
        }, $this->data);
    }
}
