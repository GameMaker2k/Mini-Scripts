<?php

// hmac hash function
function hmac($data, $key, $hash = 'sha1', $blocksize = 64)
{
    if (strlen($key) > $blocksize) {
        $key = pack('H*', $hash($key));
    }
    $key = str_pad($key, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);
    return $hash(($key ^ $opad).pack('H*', $hash(($key ^ $ipad).$data)));
}
// b64hmac hash function
function b64e_hmac($data, $key, $extdata, $hash = 'sha1', $blocksize = 64)
{
    $extdata2 = hexdec($extdata);
    $key = $key.$extdata2;
    return base64_encode(hmac($data, $key, $hash, $blocksize).$extdata);
}
// salt hmac hash function
function salt_hmac($size1 = 4, $size2 = 6)
{
    $hprand = rand(4, 6);
    $i = 0;
    $hpass = "";
    while ($i < $hprand) {
        $hspsrand = rand(1, 2);
        if ($hspsrand != 1 && $hspsrand != 2) {
            $hspsrand = 1;
        }
        if ($hspsrand == 1) {
            $hpass .= chr(rand(48, 57));
        }
        if ($hspsrand == 2) {
            $hpass .= chr(rand(65, 70));
        }
        ++$i;
    } return $hpass;
}
