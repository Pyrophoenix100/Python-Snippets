<?php
readline_completion_function(fn ($i) => array_filter(scandir($_SERVER['DOCUMENT_ROOT']), fn ($e) => str_contains($e, $i)));
