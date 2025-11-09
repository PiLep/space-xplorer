<?php

namespace App\Livewire\Concerns;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

trait MakesApiRequests
{
    /**
     * Make an authenticated API request.
     *
     * @param  string  $method
     * @param  string  $endpoint
     * @param  array  $data
     * @return array
     *
     * @throws \Exception
     */
    protected function makeApiRequest(string $method, string $endpoint, array $data = []): array
    {
        $token = Session::get('sanctum_token');

        if (! $token) {
            throw new \Exception('No authentication token found. Please log in.');
        }

        try {
            $response = Http::withToken($token)
                ->{strtolower($method)}($endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            // Handle validation errors
            if ($response->status() === 422) {
                $errors = $response->json('errors', []);
                throw new \Exception(json_encode($errors));
            }

            // Handle other errors
            $message = $response->json('message', 'An error occurred');
            throw new \Exception($message);
        } catch (RequestException $e) {
            throw new \Exception('API request failed: '.$e->getMessage());
        }
    }

    /**
     * Get the API base URL.
     */
    protected function getApiBaseUrl(): string
    {
        return config('app.url').'/api';
    }

    /**
     * Make a GET request to the API.
     */
    protected function apiGet(string $endpoint): array
    {
        return $this->makeApiRequest('GET', $this->getApiBaseUrl().$endpoint);
    }

    /**
     * Make a POST request to the API.
     */
    protected function apiPost(string $endpoint, array $data = []): array
    {
        return $this->makeApiRequest('POST', $this->getApiBaseUrl().$endpoint, $data);
    }

    /**
     * Make a PUT request to the API.
     */
    protected function apiPut(string $endpoint, array $data = []): array
    {
        return $this->makeApiRequest('PUT', $this->getApiBaseUrl().$endpoint, $data);
    }

    /**
     * Make a DELETE request to the API.
     */
    protected function apiDelete(string $endpoint): array
    {
        return $this->makeApiRequest('DELETE', $this->getApiBaseUrl().$endpoint);
    }
}

