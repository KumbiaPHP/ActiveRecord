<?php

use PHPUnit\Framework\TestCase;
use Kumbia\ActiveRecord\Metadata\Metadata;

class MetadataTest extends TestCase
{
    protected $dbName;

    protected $tableName;

    protected $schemaName;
    
    public function setUp(): void
    {
        $this->dbName     = getenv('DB');

        if (!extension_loaded('pdo_'.$this->dbName)) {
            $this->markTestSkipped(
              'The pdo_'.$this->dbName.' extension is not available.'
            );
        }

        $this->tableName  = $GLOBALS['metadata_table'];
        $this->schemaName = $GLOBALS['metadata_schema'];
    }

    protected function getMetadata(): Metadata
    {
        return Metadata::get($this->dbName, $this->tableName, $this->schemaName);
    }

    public function testInstanceOfDriverDb()
    {
        $metadata = $this->getMetadata();
        $dbDriverClass = \ucfirst($this->dbName).'Metadata';

        $this->assertInstanceOf('\\Kumbia\\ActiveRecord\\Metadata\\'.$dbDriverClass, $metadata);
    }

    public function testGetPK()
    {
        $pk = $this->getMetadata()->getPK();

        $this->assertEquals('id', $pk);
    }

    public function testGetWithDefault()
    {

        $withDefault = $this->getMetadata()->getWithDefault();

        $this->assertEquals(['activo'], $withDefault);
    }

    protected static function expectedGetFields(): array
    {
        return [
            'activo' => [
                    'Type' => 'smallint(1)',
                    'Null' => true,
                    'Default' => true,
                    'Key' => '',
                    'Auto' => false
            ],
            'email' => [
                    'Type' => 'varchar(100)',
                    'Null' => false,
                    'Default' => false,
                    'Key' => '',
                    'Auto' => false,
            ],
            'id' => [
                    'Type' => 'bigint(20)',
                    'Null' => false,
                    'Default' => false,
                    'Key' => 'PRI',
                    'Auto' => true,
            ],
            'nombre' => [
                    'Type' => 'varchar(50)',
                    'Null' => false,
                    'Default' => false,
                    'Key' => '',
                    'Auto' => false,
                ]
            ];
    }
    public function testGetFields()
    {
        $fields = $this->getMetadata()->getFields();

        $this->assertEquals(self::expectedGetFields(), $fields);
    }

    public function testGetFieldsList()
    {
        $fields = $this->getMetadata()->getFieldsList();

        $this->assertEquals(['id', 'nombre', 'email', 'activo'], $fields);
    }

    public function testGetAutoFields()
    {
        $fields = $this->getMetadata()->getAutoFields();
        
        $this->assertEquals(['id'], $fields);
    }
}
