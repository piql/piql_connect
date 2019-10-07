<?php

namespace Tests;

trait TestEnvironment {

    public function checkApplicationEnvironment(array $environment = [
        'APP_ENV' => ['testing']]) {

        $this->checkEnvironmentSettings($environment);
    }

    public function checkDatabaseEnvironment(array $environment = [
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => ':memory:']) {

        $this->checkEnvironmentSettings($environment);
    }

    public function checkEnvironmentSettings(array $environmentSettings) {
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

    public function checkDefaultEnvironment() {
        $this->checkApplicationEnvironment();
        $this->checkDatabaseEnvironment();
    }

}
