<?php
/**
 * @category    Pimcore Plugin
 * @date        06/08/2017 19:22
 * @author      Łukasz Marszałek <lmarszalek@divante.pl>
 * @copyright   Copyright (c) 2017 Divante Ltd. (https://divante.co)
 */
namespace Divante\AssetUnicodeNameBundle\EventListener;

use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Model\Element\Service;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AssetListener
 * @package Divante\AssetUnicodeNameBundle\EventListener
 */
class AssetListener
{
    const METADATA_NAME = 'unicode-name';

    /**
     * @param ElementEventInterface $event
     */
    public function setUnicodeName(ElementEventInterface $event)
    {

        if ($event instanceof AssetEvent) {
            $asset = $event->getAsset();

            $request = Request::createFromGlobals();

            $unicodeName = $request->request->get('filename');
            $metadata    = $request->request->get('metadata');

            if ($unicodeName && $asset->getType() != 'folder') {
                $asset->addMetadata(self::METADATA_NAME, "input", $unicodeName);
            } elseif (!is_null($metadata)) {
                $postMetadata = json_decode($metadata);

                if (!is_array($postMetadata)) {
                    return;
                }

                foreach ($postMetadata as $changedMetadata) {
                    if (isset($changedMetadata->name) && ($changedMetadata->name == self::METADATA_NAME)
                        && !is_null($changedMetadata->data)
                    ) {
                        $asset->setFilename(Service::getValidKey($changedMetadata->data, 'asset'));
                        break;
                    }
                }
            }

        }
    }
}