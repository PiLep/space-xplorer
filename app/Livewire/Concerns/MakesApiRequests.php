<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

trait MakesApiRequests
{
    /**
     * Make an authenticated API request.
     * Uses session cookies for authentication (Sanctum stateful authentication).
     *
     * @throws \Exception
     */
    protected function makeApiRequest(string $method, string $endpoint, array $data = []): array
    {
        // Check if user is authenticated via session
        if (! Auth::check()) {
            throw new \Exception('No authentication found. Please log in.');
        }

        try {
            // Use session cookies for authentication (Sanctum stateful)
            // Pass cookies from current request to maintain session
            $cookies = [];
            foreach (request()->cookies->all() as $name => $value) {
                $cookies[] = new \GuzzleHttp\Cookie\SetCookie([
                    'Name' => $name,
                    'Value' => $value,
                    'Domain' => request()->getHost(),
                    'Path' => '/',
                ]);
            }

            $response = Http::withCookies($cookies, request()->getHost())
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-XSRF-TOKEN' => csrf_token(),
                    'Referer' => request()->url(),
                ])
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
        // Use the same host as the current request to avoid connection issues
        $url = config('app.url');
        if (empty($url) || $url === 'http://localhost' || $url === 'http://127.0.0.1') {
            // In Docker, use the service name or localhost
            $url = request()->getSchemeAndHttpHost() ?: 'http://localhost';
        }

        return $url.'/api';
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

    /**
     * Make a public API request (without authentication token).
     *
     *
     * @throws \Exception
     */
    protected function makePublicApiRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $url = $this->getApiBaseUrl().$endpoint;

            $response = Http::timeout(10)
                ->acceptJson()
                ->contentType('application/json')
                ->{strtolower($method)}($url, $data);

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
            $errorDetails = $response->json('errors', []);
            if (! empty($errorDetails)) {
                throw new \Exception(json_encode($errorDetails));
            }
            throw new \Exception($message ?: 'HTTP '.$response->status());
        } catch (RequestException $e) {
            throw new \Exception('API request failed: '.$e->getMessage());
        } catch (\Exception $e) {
            // Re-throw to preserve error message
            throw $e;
        }
    }

    /**
     * Make a public POST request to the API.
     */
    protected function apiPostPublic(string $endpoint, array $data = []): array
    {
        // Ne pas préfixer avec getApiBaseUrl() car makePublicApiRequest le fait déjà
        return $this->makePublicApiRequest('POST', $endpoint, $data);
    }

    /**
     * Authenticate user in browser session after API authentication response.
     *
     * This is needed because API calls create a separate session context,
     * so we need to authenticate the user in the browser session for web routes.
     */
    protected function authenticateUserFromApiResponse(array $response): void
    {
        // Store token in session
        if (isset($response['data']['token'])) {
            Session::put('sanctum_token', $response['data']['token']);
        }

        // Authenticate user in browser session for web routes
        if (isset($response['data']['user']['id'])) {
            $user = User::find($response['data']['user']['id']);
            if ($user) {
                Auth::login($user);
            }
        }
    }
}
