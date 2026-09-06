![KumbiaPHP](http://proto.kumbiaphp.com/img/kumbiaphp.png)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/quality-score.png?s=f7230602070a9e9605d46544197bcdac46166612)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Tests](https://github.com/KumbiaPHP/ActiveRecord/actions/workflows/tests.yml/badge.svg?branch=dev)](https://github.com/KumbiaPHP/ActiveRecord/actions/workflows/tests.yml?query=branch%3Adev)
[![Code Climate](https://codeclimate.com/github/KumbiaPHP/ActiveRecord/badges/gpa.svg)](https://codeclimate.com/github/KumbiaPHP/ActiveRecord)

ENGLISH - [SPANISH](/README.md)

# ActiveRecord

New ActiveRecord in development

Don't use in production

## Install with composer in KumbiaPHP

Requires KumbiaPHP > 0.9RC

* Create file ***composer.json*** in to project root:

```yml
--project  
    |  
    |--vendor  
    |--default  
    |--core  
    |--composer.json        This is our file  
```

* Add the next lines:

```json
{
    "require": {
        "kumbia/activerecord" : "dev-master"
    }
}
```

* Execute command **composer install**. This installs ActiveRecord in ***vendor/kumbia/activerecord/*** and generates ***vendor/autoload.php***.

* Make sure the KumbiaPHP application loads ***vendor/autoload.php*** before using ActiveRecord.

* Continue with steps 1 and 2 of the next section.

## Configure KumbiaPHP

Requires KumbiaPHP > 0.9RC

The following steps configure the KumbiaPHP integration after Composer installation:

1. Copy [config/config_databases.php](config/config_databases.php) to ***app/config/databases.php*** and set configuration

2. (Optional) Add in ***app/libs/*** : [lite_record.php](#literecord) and/or [act_record.php](#actrecord)


### LiteRecord

For those who prefer SQL and the advantages of an ORM it includes a mini ActiveRecord

```php
<?php
//app/libs/lite_record.php

/**
 * LiteRecord 
 * For those who prefer SQL and the advantages of an ORM
 *
 * Parent class to add your methods
 *
 * @category Kumbia
 * @package ActiveRecord
 * @subpackage LiteRecord
 */

use Kumbia\ActiveRecord\LiteRecord as ORM;

class LiteRecord extends ORM
{

}
```

### ActRecord

Full ActiveRecord

```php
<?php
//app/libs/act_record.php

/**
 * ActiveRecord
 *
 * Parent class to add your methods
 *
 * @category Kumbia
 * @package ActiveRecord
 * @subpackage ActiveRecord
 */

use Kumbia\ActiveRecord\ActiveRecord;

class ActRecord extends ActiveRecord
{

}
```

# Example

## Model

```php
<?php
//app/models/people.php

class People extends ActRecord //or LiteRecord depending on your choice
{

}
```

## Controller

```php
<?php
//app/controller/people_controller.php

//Load::models('people'); This is not necessary in v1

class PeopleController extends AppController {

    public function index() {
        $this->data = People::all();
    }
    
    public function find($id) {
        $this->data = People::get($id);
    }
}
```
