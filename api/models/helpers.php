<?php

    /* convert arrays to pretty json. */

    function encode($array) {
        return json_encode($array, JSON_PRETTY_PRINT);
    }

?>