<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\URL;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;
use League\Flysystem\GoogleCloudStorage\UniformBucketLevelAccessVisibility;
use Google\Cloud\Storage\StorageClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('local')) {
            \Laravel\Telescope\Telescope::night();
        }

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register Google Cloud Storage driver
        Storage::extend('gcs', function ($app, $config) {
            $clientConfig = [
                'projectId' => $config['project_id'],
            ];

            // Only add keyFilePath if it's provided and file exists
            if (!empty($config['key_file']) && file_exists($config['key_file'])) {
                $clientConfig['keyFilePath'] = $config['key_file'];
            }

            $storageClient = new StorageClient($clientConfig);
            $bucket = $storageClient->bucket($config['bucket']);

            // Use UniformBucketLevelAccessVisibility handler to prevent ACL setting
            // This is required for buckets with uniform bucket-level access enabled
            $visibilityHandler = new UniformBucketLevelAccessVisibility();

            // Create adapter with uniform bucket-level access visibility handler
            $adapter = new GoogleCloudStorageAdapter(
                $bucket,
                $config['path_prefix'] ?? '',
                $visibilityHandler
            );

            // Create Flysystem instance with configuration
            // Don't pass visibility-related config to avoid ACL errors with uniform bucket-level access
            $flysystemConfig = [];

            $flysystem = new Filesystem($adapter, $flysystemConfig);

            // Remove visibility from config to prevent FilesystemAdapter from trying to set ACLs
            $adapterConfig = $config;
            unset($adapterConfig['visibility']);

            // Return Laravel's FilesystemAdapter which wraps the Flysystem instance
            return new FilesystemAdapter($flysystem, $adapter, $adapterConfig);
        });
    }
}
