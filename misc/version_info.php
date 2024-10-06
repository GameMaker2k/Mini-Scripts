<?php

// Version info stuff. :P
function version_info($proname, $subver, $ver, $supver, $reltype, $svnver, $showsvn)
{
    $return_var = $proname." ".$reltype." ".$subver.".".$ver.".".$supver;
    if ($showsvn === false) {
        $showsvn = null;
    }
    if ($showsvn === true) {
        $return_var .= " SVN ".$svnver;
    }
    if ($showsvn !== true && $showsvn != null) {
        $return_var .= " ".$showsvn." ".$svnver;
    }
    return $return_var;
}
