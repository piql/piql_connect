#!/usr/bin/env php

<?php
$username = 'test';
$password = 'test';
$logFilePath = '../storage/logs/laravel-' . date('Y-m-d') . '.log';
$commandFileTempPath = '/tmp/lynx_scr.txt';

function logInfo($text)
{
    echo "INFO: " . $text . PHP_EOL;
}

function exitWithError($text)
{
    echo "ERROR: " . $text . PHP_EOL;
    exit(1);
}

// Parse log
logInfo('Parsing log: ' . $logFilePath);
$callbackUrl = '';
$callbackAuthorizationKey = '';
$handle = fopen($logFilePath, "r");
if ($handle)
{
    while (($line = fgets($handle)) !== false)
    {
        if (strpos($line, '<package_uuid>') !== false)
        {
            $callbackUrl = trim($line);
        }

        if (strpos($line, 'Authorization: ') !== false)
        {
            $callbackAuthorizationKey = trim(substr($line, strlen('Authorization: ')));
        }
    }

    fclose($handle);
}
else
{
    exitWithError('Failed to read log file');
} 
logInfo('Found URL: ' . $callbackUrl);
logInfo('Found key: ' . $callbackAuthorizationKey);

$str = '';
$str .= '# Command logfile created by Lynx 2.9.0dev.4 (26 Aug 2019)' . PHP_EOL;
$str .= '# Arg0 = lynx' . PHP_EOL;
$str .= '# Arg1 = http://192.168.10.22:62081' . PHP_EOL;
$str .= '# Arg2 = -cmd_log=lynx_commands.txt' . PHP_EOL;
$str .= 'key y' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
for ($i = 0; $i < strlen($username); $i++)
{
    $str .= 'key ' . $username[$i] . PHP_EOL;
}
$str .= 'key Down Arrow' . PHP_EOL;
for ($i = 0; $i < strlen($password); $i++)
{
    $str .= 'key ' . $password[$i] . PHP_EOL;
}
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^J' . PHP_EOL;
$str .= 'key y' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^J' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^J' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^J' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^U' . PHP_EOL;
for ($i = 0; $i < strlen($callbackUrl); $i++)
{
    if ($callbackUrl[$i] == ' ')
    {
        $str .= 'key <space>' . PHP_EOL;
    }
    else
    {
        $str .= 'key ' . $callbackUrl[$i] . PHP_EOL;
    }
}
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^U' . PHP_EOL;
for ($i = 0; $i < strlen($callbackAuthorizationKey); $i++)
{
    if ($callbackAuthorizationKey[$i] == ' ')
    {
        $str .= 'key <space>' . PHP_EOL;
    }
    else
    {
        $str .= 'key ' . $callbackAuthorizationKey[$i] . PHP_EOL;
    }
}
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^J' . PHP_EOL;
$str .= 'key y' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^J' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^J' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^U' . PHP_EOL;
for ($i = 0; $i < strlen($callbackUrl); $i++)
{
    if ($callbackUrl[$i] == ' ')
    {
        $str .= 'key <space>' . PHP_EOL;
    }
    else
    {
        $str .= 'key ' . $callbackUrl[$i] . PHP_EOL;
    }
}
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^U' . PHP_EOL;
for ($i = 0; $i < strlen($callbackAuthorizationKey); $i++)
{
    if ($callbackAuthorizationKey[$i] == ' ')
    {
        $str .= 'key <space>' . PHP_EOL;
    }
    else
    {
        $str .= 'key ' . $callbackAuthorizationKey[$i] . PHP_EOL;
    }
}
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key Down Arrow' . PHP_EOL;
$str .= 'key ^J' . PHP_EOL;
$str .= 'key y' . PHP_EOL;
$str .= 'key q' . PHP_EOL;
$str .= 'key y' . PHP_EOL;

// Run Lynx
if (file_put_contents($commandFileTempPath, $str) === false)
{
    exitWithError('Failed to write to file: ' . $commandFileTempPath);
}
$command = 'lynx http://127.0.0.1:62081 -cmd_script=' . $commandFileTempPath;
if (system($command) === false)
{
    exitWithError('Failed to execute command: ' . $command);
}
if (!unlink($commandFileTempPath))
{
    exitWithError('Failed to delete file: ' . $commandFileTempPath);
}

logInfo('Finished successfully');
?>
