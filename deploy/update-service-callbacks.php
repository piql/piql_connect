#!/usr/bin/env php

<?php
$username = 'test';
$password = 'test';
$logDir = '../storage/logs/';
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
$callbackUrl = '';
$callbackAuthorizationKey = '';
if (is_dir($logDir))
{
    $logFiles = scandir($logDir, SCANDIR_SORT_DESCENDING);
    foreach ($logFiles as $logFile)
    {
        $logFile = $logDir . $logFile;
        logInfo('Found file: ' . $logFile);
        if (strpos($logFile, 'laravel-') !== false && substr($logFile, strlen($logFile) - 4) == '.log')
        {
            logInfo('Parsing log: ' . $logFile);
            $handle = fopen($logFile, "r");
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

                if (strlen($callbackUrl) != 0 || strlen($callbackAuthorizationKey) != 0)
                {
                    if (strlen($callbackUrl) == 0 || strlen($callbackAuthorizationKey) == 0)
                    {
                        exitWithError('Could not find both url and key: ' . $callbackUrl . ' ' . $callbackAuthorizationKey);
                    }

                    break;
                }
            }
            else
            {
                exitWithError('Failed to read file: ' . $logFile);
            }
        }
    }
}
else
{
    exitWithError('Failed to read log directory: ' . $logDir);
}
if (strlen($callbackUrl) == 0 || strlen($callbackAuthorizationKey) == 0)
{
    exitWithError('Failed to read service callback info from logs');
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
