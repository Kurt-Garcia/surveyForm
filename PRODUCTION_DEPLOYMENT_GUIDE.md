# Laravel Survey Broadcasting - Production Deployment Guide

## 🚀 Quick Start Commands

### 1. Start Queue Worker (Required for Broadcasting)
```powershell
cd c:\xampp\htdocs\surveyForm
php artisan queue:work --queue=high,medium,default --tries=3 --timeout=60
```

### 2. Start Laravel Development Server
```powershell
cd c:\xampp\htdocs\surveyForm
php artisan serve --port=8001
```

### 3. Access Admin Panel
- URL: `http://localhost:8001/admin/login`
- Navigate to Surveys → Select Survey → Click Broadcast Button

## 📋 Production Checklist

### Environment Configuration
- [ ] **Email Settings**: Verify SMTP configuration in `.env`
- [ ] **Queue Driver**: Ensure `QUEUE_CONNECTION=database` in `.env`
- [ ] **Database**: Confirm `jobs`, `job_batches`, `failed_jobs` tables exist
- [ ] **Cache**: Redis/Memcached recommended for production cache

### Queue Worker Management
- [ ] **Process Manager**: Use Supervisor or similar for production
- [ ] **Auto-Restart**: Configure automatic restart on failure
- [ ] **Multiple Workers**: Scale workers based on email volume
- [ ] **Monitoring**: Set up alerts for failed jobs

### Performance Optimization
- [ ] **Email Provider**: Switch from Mailtrap to production SMTP (SendGrid, AWS SES)
- [ ] **Rate Limiting**: Configure SMTP rate limits to avoid blocking
- [ ] **Batch Sizes**: Adjust based on server capacity
- [ ] **Memory Limits**: Monitor PHP memory usage

## 🔧 Production Configuration Examples

### Supervisor Configuration (Linux)
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=forge
numprocs=3
redirect_stderr=true
stdout_logfile=/path/to/your/project/worker.log
stopwaitsecs=3600
```

### Windows Service (NSSM)
```powershell
# Install NSSM
nssm install LaravelWorker "php" "c:\path\to\project\artisan queue:work --sleep=3 --tries=3"
nssm set LaravelWorker AppDirectory "c:\path\to\project"
nssm start LaravelWorker

php artisan queue:work --queue=high --tries=3 --timeout=120      
php artisan queue:work --queue=medium --tries=2 --timeout=300       
php artisan queue:work --queue=default               
```

### Environment Variables (.env)
```env
# Email Configuration (Production)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=your_sendgrid_username
MAIL_PASSWORD=your_sendgrid_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="Your Company"

# Queue Configuration
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database

# Cache (Production)
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 📊 Monitoring & Maintenance

### Daily Checks
```powershell
# Check failed jobs
php artisan queue:failed

# Clear failed jobs (if resolved)
php artisan queue:flush

# Monitor queue size
php artisan queue:monitor database --max=100
```

### Performance Metrics
```powershell
# Job statistics
php artisan horizon:stats  # If using Laravel Horizon

# Check queue worker status
php artisan queue:work --queue=high,medium,default --once
```

### Log Monitoring
```powershell
# Recent activity
Get-Content storage/logs/laravel.log -Tail 50

# Email-specific logs
Get-Content storage/logs/laravel.log | Select-String "Survey invitation"
```

## 🚨 Troubleshooting

### Common Issues

#### Queue Worker Not Processing
```powershell
# Restart queue worker
php artisan queue:restart

# Clear cache
php artisan cache:clear
php artisan config:clear
```

#### Email Sending Failures
```powershell
# Test email configuration
php artisan tinker
# >>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

#### High Memory Usage
```powershell
# Monitor memory
php artisan queue:work --memory=512 --timeout=60

# Restart worker periodically
php artisan queue:work --max-time=3600
```

### Performance Tuning

#### For High Volume (1000+ emails)
```php
// In ProcessSurveyBroadcastJob.php
// Increase batch processing delay
->delay(now()->addSeconds(rand(1, 10)));

// Reduce concurrent jobs
// Run multiple workers instead
```

#### For Low Latency
```php
// In SendSurveyInvitationJob.php
// Reduce delays
->delay(now()->addSeconds(rand(1, 3)));

// Use priority queues effectively
$this->onQueue('high');
```

## 📈 Scaling Recommendations

### Small Scale (< 100 customers)
- **Workers**: 1 queue worker
- **Email Provider**: Mailtrap/local SMTP
- **Cache**: File-based cache

### Medium Scale (100-1000 customers)
- **Workers**: 2-3 queue workers
- **Email Provider**: SendGrid/Mailgun
- **Cache**: Redis
- **Monitoring**: Basic log monitoring

### Large Scale (1000+ customers)
- **Workers**: 5+ queue workers (scale horizontally)
- **Email Provider**: AWS SES/SendGrid with dedicated IP
- **Cache**: Redis Cluster
- **Monitoring**: Laravel Horizon + Application monitoring
- **Database**: Optimize with indexes, connection pooling

## 🔐 Security Notes

- **Rate Limiting**: Implement API rate limiting for broadcast endpoints
- **Authentication**: Ensure admin routes are properly protected
- **Email Validation**: Validate email addresses before sending
- **Error Handling**: Don't expose sensitive information in error messages
- **Logging**: Monitor for suspicious broadcast patterns

## ✅ Success Metrics

- **Response Time**: < 1 second for broadcast initiation
- **Processing Speed**: 10-50 emails per minute (depends on SMTP provider)
- **Error Rate**: < 5% failed emails
- **Recovery Time**: Failed jobs retry within 60 seconds
- **Monitoring**: Real-time progress updates work consistently

## 🆘 Emergency Procedures

### Stop All Broadcasting
```powershell
# Stop queue workers
php artisan queue:restart

# Clear pending jobs (if needed)
php artisan queue:clear database
```

### Urgent Email Fixes
```powershell
# Retry all failed jobs
php artisan queue:retry all

# Force process specific job
php artisan queue:work --once --queue=high
```

This system is now production-ready with professional-grade performance and monitoring capabilities.
