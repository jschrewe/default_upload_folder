<?php
namespace Kooperationsstelle\DefaultUploadFolder\Hooks;

/*
 * This source file is proprietary property of Beech Applications B.V.
 * Date: 06-04-2016
 * All code (c) Beech Applications B.V. all rights reserved
 */
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Resource\Exception\FolderDoesNotExistException;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Class DefaultUploadFolder
 */
class DefaultUploadFolder {
    /**
     * Get default upload folder
     *
     * @param array $params
     * @param BackendUserAuthentication $backendUserAuthentication
     * @return Folder
     */
    public function getDefaultStorage($params, BackendUserAuthentication $backendUserAuthentication) {
      \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($params);
      $siteFinder = new SiteFinder();
      $site = $siteFinder->getSiteByPageId($params['pid']);
      try {
          $storage = $site->getAttribute('defaultStorageUid');
      } catch (\InvalidArgumentException $e) {
      $resourceFactory = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance();
      $storage = $resourceFactory->getDefaultStorage();
    }


            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($uploadFolder);

            /** @var Folder $uploadFolder */
            $uploadFolder = $params['uploadFolder'];
            $table = $params['table'];
            $field = $params['field'];
            $pageTs = BackendUtility::getPagesTSconfig($params['pid']);

            $subFolder = $backendUserAuthentication->getTSConfig(
                'default_upload_folders.' . $table . '.' . $field,
                $pageTs
            );
            if ($subFolder['value'] === null) {
                $subFolder = $backendUserAuthentication->getTSConfig(
                    'default_upload_folders.' . $table,
                    $pageTs
                );
            }
            if ($subFolder['value'] === null) {
                $subFolder = $backendUserAuthentication->getTSConfig(
                    'default_upload_folders.defaultForAllTables',
                    $pageTs
                );
            }
        }

        // Folder by combined identifier
        if (preg_match('/[0-9]+:/', $subFolder['value'])) {
            try {
                $uploadFolder = ResourceFactory::getInstance()->getFolderObjectFromCombinedIdentifier(
                    $subFolder['value']
                );
            } catch (FolderDoesNotExistException $e) {
                // todo: try to create the folder
            }
        }

        if (
            $uploadFolder instanceof Folder
            &&
            $subFolder['value'] !== null
            &&
            $uploadFolder->hasFolder($subFolder['value'])
        ) {
            $uploadFolder = $uploadFolder->getSubfolder($subFolder['value']);
        }

        return $uploadFolder;
    }
}
