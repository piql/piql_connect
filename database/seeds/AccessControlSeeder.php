<?php

use App\AccessControl;
use App\Enums\AccessControlType;
use App\Services\AccessControlManager;
use App\Traits\SeederOperations;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    use SeederOperations;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($this->seedFromFile(function ($role) {
            $this->create($role, AccessControlType::Role);
        })) {
            return;
        }
    }

    private function create(array $data, $type = AccessControlType::Permission)
    {
        $data['type'] = ($data['type'] == null) ? $type : $data['type'];
        $ac = AccessControl::where('name', $data['name'])->first();
        if ($ac == null) {
            $ac = new AccessControl(collect($data)->only(['name', 'type', 'description'])->all());
            try {
                $ac->save();
            } catch (Exception $e) {
                $typeName = AccessControlType::getDescription($type);
                throw new Exception("Failed to create $typeName $ac->name. " . $e->getMessage());
            }
        }
        if (!array_key_exists('permissions', $data) || empty($data['permissions']))
            return $ac;
        $permissions = [];
        foreach ($data['permissions'] as $p) {
            $pm = $this->create((array) $p);
            if ($pm != null && $ac->type == AccessControlType::Role)
                $permissions[] = $pm->id;
        }
        if (!empty($permissions))
            AccessControlManager::addPermissionsToRole($ac->id, $permissions);
        return $ac;
    }
}
