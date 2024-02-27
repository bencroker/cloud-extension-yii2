<?php

namespace craft\cloud;

enum HeaderEnum: string
{
    case CACHE_TAG = 'Cache-Tag';
    case CACHE_PURGE_TAG = 'Cache-Purge-Tag';
    case CACHE_PURGE_PREFIX = 'Cache-Purge-Prefix';
    case CACHE_PURGE_HOST = 'Cache-Purge-Host';
    case CACHE_CONTROL = 'Cache-Control';
    case AUTHORIZATION = 'Authorization';
    case DEV_MODE = 'Dev-Mode';
}
