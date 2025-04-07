# UNIL - Service Status Monitor

This project displays the status of internal and external web services for the University of Lausanne (UNIL).  
Developed as part of a technical challenge for CSE UNIL.

## Quick Start

### 1. Clone the repository

```bash
git clone https://github.com/eugene-pascal/unil-de-lausanne.git
cd unil-de-lausanne
```

### 2. Copy the environment configuration

```bash
cp .env_copy .env
```

> The application uses **SQLite** by default.  
> It is set to`DB_CONNECTION=sqlite` .

### 3. Install dependencies

```bash
composer install
```

### 4. Generate key 

```bash
php artisan key:generate
```

### 5. Run the database migrations

```bash
php artisan migrate
```

### 6. Run the service status check

```bash
php artisan services:check
```

> You can add this command to your `cron` for automated periodic checks. Below example with cron:
> * * * * * cd /path/to/your/project && /usr/bin/php artisan services:check > /dev/null 2>&1

---


### 7. Launch your server locally 

```bash
php artisan serve
```
> Open link http://127.0.0.1:8000

## Technical Overview

### Service Configuration

All services are defined in the config file:

```
config/services_status.php
```

Each service is described like this:

```php
'Compilatio' => [
    'url' => 'https://app.compilatio.net/api/public/alerts',
    'type' => 'json',
    'check' => [
        'param' => 'status.message',
        'value' => 'OK',
        'error' => 'data.alerts'
    ]
],
'Recorder' => [
        'url' => 'https://cse.unil.ch/miris/?q=POL-A',
        'type' => 'html',
        'check' => [
            'function' =>'detectServiceRecorder',
        ]
    ],
'WordPress' => [
        'url' => 'https://sepia2.unil.ch/wp/',
        'type' => 'html',
        'check' => [
            'search' =>'Aucun site n&rsquo;est disponible à cette adresse',
            'success' => false,
            'errorMessage' => 'Aucun site n\'est disponible à cette adresse.',
        ]
    ],

```

> This allows you to define which JSON/SOAP response fields indicate success or failure.
> like "check.params" indicates on field SUCCESS / FAILURE and "check.value" contains value on SUCCESS else take message from "check.error"
> Similar thing I did for HTML page also. Some HTML page has "'function' =>'detectServiceRecorder' in the config". It indicates that the system needs to launch extra function to analyze the content HTML page.

---

###  Architecture

#### Models

- `app/Models/ServiceStatus.php` - model stores history of checks
- `app/Models/ServiceFailure.php` - model stores service downtime (failures)

#### Service check logic

Located in `app/Services/Checkers/`:

- `HtmlServiceChecker.php`
- `JsonServiceChecker.php`
- `SoapServiceChecker.php`
- `ServiceCheckerInterface.php`
- `ServiceCheckerFactory.php` - selects the appropriate checker based on service **type**

> Each type of page has its own checker. This architecture is modular and easy to maintain and extend.

#### Uptime calculation

Implemented in `app/Services/Uptimes/ServiceCalculateUptimes.php`  
This class performs uptime calculations using **app/Models/**, but is fully decoupled from them - allowing for **flexible and reusable logic**.

#### Status constants

Service statuses are defined using an enum:

```php
app/Enums/ServiceStatusEnum.php
```

> Improves code readability and reduces risk of typos.

---

## Demo

[https://unil-test.mrpascal.com/](https://unil-test.mrpascal.com/)

---

## Contact

Developed by [@eugene-pascal](https://github.com/eugene-pascal)

CV [https://cv.mrpascal.com/](https://cv.mrpascal.com/)  

For questions: `webradsupport@gmail.com` or `me@mrpascal.com`