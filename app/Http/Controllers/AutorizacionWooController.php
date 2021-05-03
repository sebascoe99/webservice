<?php

namespace App\Http\Controllers;

class AutorizacionWooController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function header()
    {
        $username = 'ck_410466fb40cfc46b2505de47f2ccd8e42932f628';
        $password = 'cs_3b3d7140c97450f494fef640118e066f5e4b0093';


        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode("$username:$password")
        );
        return $headers;
    }


    public function headerGravity()
    {
        $username = 'ck_a37f34f5333907983f0598d7db2490d88eb9b959';
        $password = 'cs_962fc6f943a9cd06fa7150a0d29db1e94e1a0b0d';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode("$username:$password")
        );
        return $headers;
    }
}
