<?php

namespace App\Traits;

trait SeederOperations
{
    /**
     * seedFromFile looks for a json file within the in the same directory as the calling class.
     * The file must be called <classname>.json. The createMethod is called for each object
     * defined in the json file
     *
     * @param $createMethod
     * @return bool return false if file don't exist or contains no valid data
     * @throws \ReflectionException
     */

    private function seedFromFile(callable $createMethod) : bool  {

        $class = new \ReflectionClass($this);
        $jsonSeederFileName = preg_replace('/\..*$/', '.json', $class->getFileName());

        if(!file_exists($jsonSeederFileName)) {
            print('Seeder file no found: '.$jsonSeederFileName."\n");
            return false;
        }

        $jsonString = file_get_contents($jsonSeederFileName);
        $data = json_decode($jsonString);
        if(!isset($data->data)) {
            print('Empty data set in: '.$jsonSeederFileName."\n");
            return false;
        }

        foreach ($data->data as $objects) {
            foreach ($objects->attributes->collection as $object) {
                $createMethod((array)$object);
            }
        }
        return true;
    }


}
