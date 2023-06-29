<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\AutoLanguageSwitcher\Api;

/**
 * Interface TranslationRepositoryInterface
 * @package Magefan\Translation\Api
 */
interface SuggestedStoreInterface
{
    /**
     * @return string
     */
    public function get();
}
