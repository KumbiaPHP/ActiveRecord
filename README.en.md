![KumbiaPHP](http://proto.kumbiaphp.com/img/kumbiaphp.png)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/quality-score.png?s=f7230602070a9e9605d46544197bcdac46166612)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Code Coverage](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/badges/coverage.png?s=58997633701e84050c0ebd5334f3eb1bb8b7ad42)](https://scrutinizer-ci.com/g/KumbiaPHP/ActiveRecord/)
[![Build Status](https://travis-ci.org/KumbiaPHP/ActiveRecord.png?branch=master)](https://travis-ci.org/KumbiaPHP/ActiveRecord)
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

* Execute command **composer install**

* Continue with steps number 2 and 3 of the next section.

## Install in KumbiaPHP

Requires KumbiaPHP > 0.9RC

1. Copy folder ***lib/Kumbia*** in vendor. (vendor/Kumbia/ActiveRecord/..)

2. Copy [config_databases.php](/config_databases.php) in ***app/config/databases.php*** and set configuration

3. Add in ***app/libs/*** : [lite_record.php](#literecord) and/or [act_record.php](#actrecord)


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
