# Elasticsearch Setup Guide for Dental ERP

## Overview
This application uses Elasticsearch for fast patient search capabilities with fallback to database search when Elasticsearch is unavailable.

## Installation & Setup

### 1. Install Elasticsearch

#### Option A: Using Docker (Recommended)
```bash
# Pull and run Elasticsearch
docker run -d --name elasticsearch \
  -p 9200:9200 \
  -e "discovery.type=single-node" \
  -e "xpack.security.enabled=false" \
  elasticsearch:7.17.2
```

#### Option B: Local Installation
1. Download Elasticsearch 7.17.2 from https://www.elastic.co/downloads/elasticsearch
2. Extract and run: `bin/elasticsearch` (Linux/Mac) or `bin\elasticsearch.bat` (Windows)

### 2. Configure Environment
Add to your `.env` file:
```
ELASTICSEARCH_HOSTS=localhost:9200
```

### 3. Verify Connection
```bash
php artisan elasticsearch:health
```

### 4. Index Patients Data
```bash
php artisan index:patients
```

## Features

### Optimized Search
- **Primary**: Elasticsearch with fuzzy matching and phrase prefix search
- **Fallback**: Database search when Elasticsearch is unavailable
- **Error Handling**: Graceful degradation with logging

### Health Monitoring
- Connection health checks
- Cluster status monitoring
- Index existence validation

## Troubleshooting

### "No alive nodes found in your cluster"
1. **Check Elasticsearch Status**
   ```bash
   php artisan elasticsearch:health
   ```

2. **Verify Service is Running**
   - Docker: `docker ps | grep elasticsearch`
   - Local: Check if port 9200 is accessible: `curl http://localhost:9200`

3. **Check Configuration**
   - Verify `ELASTICSEARCH_HOSTS` in `.env`
   - Ensure correct host and port

4. **Network Issues**
   - Check firewall settings
   - Verify port 9200 is open
   - Test connectivity: `telnet localhost 9200`

### Performance Issues
1. **Index Optimization**
   ```bash
   # Reindex all patients
   php artisan index:patients
   ```

2. **Monitor Cluster Health**
   ```bash
   curl http://localhost:9200/_cluster/health?pretty
   ```

### Fallback Mode
If Elasticsearch is down, the application automatically:
- Falls back to database search
- Logs warnings for monitoring
- Maintains full functionality

## API Endpoints

### Patient Search
```http
GET /api/patients?search=john&per_page=50&dateFilter=today
```

**Parameters:**
- `search`: Search term for name, email, phone
- `per_page`: Results per page (default: 50)
- `dateFilter`: all|today|recent|custom
- `startDate`: Custom date range start (YYYY-MM-DD)
- `endDate`: Custom date range end (YYYY-MM-DD)

## Development Commands

```bash
# Check Elasticsearch health
php artisan elasticsearch:health

# Index all patients
php artisan index:patients

# Clear application cache
php artisan cache:clear

# View logs
tail -f storage/logs/laravel.log
```

## Production Considerations

1. **Elasticsearch Cluster**
   - Use multiple nodes for high availability
   - Configure proper security (authentication, SSL)
   - Set up monitoring (Kibana, Metricbeat)

2. **Application Settings**
   - Increase timeout for large datasets
   - Configure proper connection pooling
   - Set up log rotation

3. **Backup Strategy**
   - Regular Elasticsearch snapshots
   - Database backups as fallback
   - Test restore procedures

## Configuration Options

### config/elasticsearch.php
```php
return [
    'hosts' => [env('ELASTICSEARCH_HOSTS', 'localhost:9200')],
    'retries' => 1,
    'timeout' => 5,
    'connection_pool' => '\\Elasticsearch\\ConnectionPool\\StaticNoPingConnectionPool',
    'ssl_verification' => false
];
```

### Environment Variables
```
ELASTICSEARCH_HOSTS=localhost:9200
ELASTICSEARCH_USERNAME=elastic (if security enabled)
ELASTICSEARCH_PASSWORD=password (if security enabled)
```