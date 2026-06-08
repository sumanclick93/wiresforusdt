<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared successfully! The server is now loading your new code.";
} else {
    echo "OPcache is not enabled on this server. Your files should be loading from disk.";
}
?>