<?php

interface CacheObject extends JsonSerializable
{

    /**
     * The key to use for caching data.
     *
     * @return string The key used.
     */
    public function getCacheKey(): string;
}

?>
