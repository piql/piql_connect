<?php


namespace App;

use Closure;
use Optimus\FineuploaderServer\Config\Config;
use Optimus\FineuploaderServer\File\Edition;
use Optimus\FineuploaderServer\Middleware\Naming\ThumbnailSuffixStrategy;
use Optimus\Onion\LayerInterface;

class BypassMiddleware implements LayerInterface
{
    const editionKey = "thumbnail";
    private $config;
    private $uploaderConfig;

    public function __construct(array $config = [], Config $uploaderConfig)
    {
        $this->config = $config;
        $this->uploaderConfig = $uploaderConfig;
    }

    public function peel($object, Closure $next)
    {

        if ($object->getType() !== 'image') {
            return $next($object);
        }

        $edition = new Edition(self::editionKey, $object->getUploaderPath(), $object->getUploaderPath(), [
            'type' => 'file',
        ], true);

        $object->addEdition($edition);

        return $next($object);
    }

}
