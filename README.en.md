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

###Using LiteRecord methods

####Filtering data

```php
    //get all as array of records
    $rows = People::all();
    echo $row[0]->name;

    //get by primary key as record
    $row = People::get($peopleId);
    echo $row->name;

    //filter as array of records
    $rows = People::filter("WHERE name LIKE ?", [$peopleName]);
    echo $rows[0]->name;

    //filter by sql as record
    $row = People::first("SELECT * FROM people WHERE name = :name", [":name" => $peopleName]);
    echo $row->name;

    //filter by sql as array of records
    $rows = People::all("SELECT * FROM people WHERE hire_date >= ?", [$hireDate]);
    echo $rows[0]->name;
```

####DML / Insert, update, delete
```php
    //adding a new record
    $peopleObj = new People();
    $peopleObj->create([
        'name' => 'Edgard Baptista',
        'job_title' => 'Accountant',
        'hire_date' => date('Y-m-d'),
        'active' => 1
    ]); //returns True or False on success or fail

    //adding a new record alternative
    $peopleObj = new People();
    $peopleObj->save([
        'name' => 'Edgard Baptista',
        'job_title' => 'Accountant',
        'hire_date' => date('Y-m-d'),
        'active' => 1
    ]); //returns True or False on success or fail


    //updating a record
    //first find the record to update
    $peopleObj = People::get($peopleId);

    $peopleObj->update([
        'name' => 'Edgard Baptista Jr',
        'active' => 0
    ]); //returns True or False on success or fail

    //updating a record alternative
    //first find the record to update
    $peopleObj = People::get($peopleId);

    $peopleObj->save([
        'name' => 'Edgard Baptista Jr',
        'active' => 0
    ]); //returns True or False on success or fail


    //deleting a record by primary key
    People::delete($peopleId);
    
```
