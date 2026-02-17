<?php

namespace App\Console\Commands;

use App\Models\Patient;
use Illuminate\Console\Command;

class CheckElasticsearchHealth extends Command
{
    protected $signature = 'elasticsearch:health';
    protected $description = 'Check Elasticsearch cluster health and connectivity';

    public function handle()
    {
        $this->info('Checking Elasticsearch connectivity...');
        
        if (Patient::isElasticsearchAvailable()) {
            $this->info('✅ Elasticsearch is available and responding');
            
            try {
                $client = Patient::getElasticsearchClient();
                $health = $client->cluster()->health();
                
                $this->table(
                    ['Property', 'Value'],
                    [
                        ['Cluster Name', $health['cluster_name'] ?? 'N/A'],
                        ['Status', $health['status'] ?? 'N/A'],
                        ['Number of Nodes', $health['number_of_nodes'] ?? 'N/A'],
                        ['Active Primary Shards', $health['active_primary_shards'] ?? 'N/A'],
                        ['Active Shards', $health['active_shards'] ?? 'N/A'],
                    ]
                );
                
                // Check if patients index exists
                $indexExists = $client->indices()->exists(['index' => 'patients']);
                if ($indexExists) {
                    $this->info('✅ Patients index exists');
                } else {
                    $this->warn('⚠️  Patients index does not exist. Run: php artisan index:patients');
                }
                
            } catch (\Exception $e) {
                $this->error('❌ Error getting cluster info: ' . $e->getMessage());
            }
        } else {
            $this->error('❌ Elasticsearch is not available');
            $this->info('Common solutions:');
            $this->info('1. Start Elasticsearch service');
            $this->info('2. Check ELASTICSEARCH_HOSTS in .env file');
            $this->info('3. Verify network connectivity to Elasticsearch cluster');
            $this->info('4. Check firewall and port accessibility');
        }
    }
}