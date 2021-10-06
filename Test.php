<?php

namespace App;

use App\Models\Company;
use App\Models\Travel;
use Throwable;

class TestScript
{
    /**
     * Execute the script.
     *
     * @return void
     */
    public function execute(): void
    {
        $start = microtime(true);

        try {
            $result = (new Company())
                ->setTravelCosts((new Travel())->getTotalTravelCostPerCompany())
                ->buildNestedCompanyData(true);
        } catch (Throwable $error) {
            $result = [ 'error' => $error->getMessage() ];
        }

        echo json_encode($result);
        echo 'Total time: ' .  (microtime(true) - $start);
    }
}
