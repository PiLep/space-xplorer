<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Illuminate\Console\Command;

class SetupMinIO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:setup 
                            {--bucket= : Bucket name to create (defaults to AWS_BUCKET from .env)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup MinIO bucket for local development';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $bucket = $this->option('bucket') ?? config('filesystems.disks.s3.bucket');

        if (! $bucket) {
            $this->error('❌ No bucket specified. Use --bucket option or set AWS_BUCKET in .env');

            return Command::FAILURE;
        }

        $this->info("🔧 Setting up MinIO bucket: {$bucket}");
        $this->newLine();

        try {
            // Check if S3 disk is configured
            $endpoint = config('filesystems.disks.s3.endpoint');
            if (! $endpoint) {
                $this->warn('⚠️  AWS_ENDPOINT not set. This command is for MinIO setup.');
                $this->line('Set AWS_ENDPOINT=http://minio:9000 in your .env for local development.');

                return Command::FAILURE;
            }

            // Create S3 client
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region', 'us-east-1'),
                'endpoint' => $endpoint,
                'use_path_style_endpoint' => config('filesystems.disks.s3.use_path_style_endpoint', false),
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);

            // Check if bucket exists
            $this->info("Checking if bucket '{$bucket}' exists...");

            if ($s3Client->doesBucketExist($bucket)) {
                $this->comment("✅ Bucket '{$bucket}' already exists.");
            } else {
                // Create bucket
                $this->info("Creating bucket '{$bucket}'...");
                $s3Client->createBucket([
                    'Bucket' => $bucket,
                ]);
                $this->info("✅ Bucket '{$bucket}' created successfully.");
            }

            // Set bucket policy for public access (optional)
            $this->newLine();
            if ($this->confirm('Make bucket public for image access?', true)) {
                $policy = json_encode([
                    'Version' => '2012-10-17',
                    'Statement' => [
                        [
                            'Effect' => 'Allow',
                            'Principal' => ['AWS' => ['*']],
                            'Action' => ['s3:GetObject'],
                            'Resource' => ["arn:aws:s3:::{$bucket}/*"],
                        ],
                    ],
                ]);

                try {
                    $s3Client->putBucketPolicy([
                        'Bucket' => $bucket,
                        'Policy' => $policy,
                    ]);
                    $this->info('✅ Bucket policy set to public.');
                } catch (\Exception $e) {
                    $this->warn("⚠️  Could not set bucket policy: {$e->getMessage()}");
                    $this->comment('You can set it manually in MinIO console (http://localhost:9001)');
                }
            }

            $this->newLine();
            $this->info('✅ MinIO setup complete!');
            $this->line("Bucket: {$bucket}");
            $this->line("Endpoint: {$endpoint}");
            $this->line('Console: http://localhost:9001');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to setup MinIO:');
            $this->line($e->getMessage());
            $this->newLine();
            $this->comment('Make sure MinIO is running: ./vendor/bin/sail up -d');
            $this->comment('Check your .env configuration for AWS_* variables');

            return Command::FAILURE;
        }
    }
}
