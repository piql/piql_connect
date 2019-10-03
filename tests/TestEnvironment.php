<?php

namespace Tests;

trait TestEnvironment {

    public function validApplicationEnvironment(array $environment = [
        'APP_ENV' => ['testing']]) {

        $this->validEnvironmentSettings($environment);
    }

    public function validDatabaseEnvironment(array $environment = [
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => ':memory:']) {

        $this->validEnvironmentSettings($environment);
    }

    public function validEnvironmentSettings(array $environmentSettings) {
        foreach ($environmentSettings as $key => $environmentSetting) {
            $envValue = env($key);
            $this->assertTrue(isset($envValue), 'Environment variable '.$key.' is missing');
            $this->assertContains(
                env($key),
                $environmentSetting,
                $key.' is not valid. Expected values are '.
                (is_array($environmentSetting) ? implode(', ', $environmentSetting) : $environmentSetting).
                ' but found '.env($key)
            );
        }
    }

    public function validDefaultEnvironment() {
        $this->validApplicationEnvironment();
        $this->validDatabaseEnvironment();
    }

}
