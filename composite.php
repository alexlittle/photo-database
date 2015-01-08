<?php

set_time_limit(0);
require_once "lib/composite-lib.php";

define("PIXEL_SIZE",5);

getConnection();
//cacheThumbnails(PIXEL_SIZE);
//cacheColours();
createComposite("/home/alex/temp/tree.resized.jpg");
//createComposite("/home/alex/temp/composite100.jpg");
cleanUpDB();
