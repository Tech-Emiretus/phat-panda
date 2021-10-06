<?php

namespace App\Models;

use Exception;

class Model
{
    /**
     * Model's data.
     *
     * @var array
     */
    public array $data = [];

    /**
     * Instantiate the model.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setupData();
    }

    /**
     * Setup the data for this model from the api.
     *
     * @throws \Exception
     * @return void
     */
    protected function setupData(): void
    {
        $data = file_get_contents(static::API_ENDPOINT);

        if ($data === false) {
            throw new Exception('Could not load travel list data from api.');
        }

        $this->data = json_decode($data, true);
    }
}
