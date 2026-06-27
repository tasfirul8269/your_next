<?php

namespace Frooxi\Installer\Listeners;

use Frooxi\User\Repositories\AdminRepository;
use GuzzleHttp\Client;

class Installer
{
    /**
     * Api endpoint
     *
     * @var string
     */
    protected const API_ENDPOINT = 'https://updates.frooxi.com/api/updates';

    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(protected AdminRepository $adminRepository) {}

    /**
     * After Frooxi is successfully installed
     *
     * @return void
     */
    public function installed()
    {
        $admin = $this->adminRepository->first();

        $httpClient = new Client;

        try {
            $httpClient->request('POST', self::API_ENDPOINT, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'domain' => config('app.url'),
                    'email' => $admin?->email,
                    'name' => $admin?->name,
                    'country_code' => config('app.default_country') ?? 'IN',
                ],
            ]);
        } catch (\Exception $e) {
            /**
             * Skip the error
             */
        }
    }
}
