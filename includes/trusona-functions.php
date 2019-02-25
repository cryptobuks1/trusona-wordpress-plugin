<?php

function random_hex() {
  return hash('ripemd160', random_bytes(2048));
}

function trusona_custom_login($url, $allow_wp_form)
{
    $allow_wp_form = apply_filters('trusona_allow_wp_form', $allow_wp_form, $url);

    $data = '<div>';

    if ($allow_wp_form) {
        $data .= '<style type="text/css">form > p {display: none;} p#nav {display: none;}</style>';
    }

    $nonce = random_hex();
    $state = random_hex();

$v2html = <<<HTML

<div class="main">
<p><a id="login-button" class="trusona-no-passwords-login-button" href="#">Sign In</a></p>
<form id="oidc_form" style="visibility:hidden;" action="https://gateway.staging.trusona.net/oidc" method="POST">
  <input type="hidden" name="scope" value="openid profile email emails" />
  <input type="hidden" name="response_mode" value="form_post" />
  <input type="hidden" name="response_type" value="id_token" />
  <input type="hidden" name="client_id" value="wpuat" />
  <input type="hidden" name="redirect_uri" value="https://54.202.19.37/499eda0e-787f-4f17-8b9d-08b5fac023da/wp-admin/admin-ajax.php?action=trusona_openid-callback" />
  <input type="hidden" name="nonce" value="$nonce" />
  <input type="hidden" name="state" value="$state" />
</form>
<script>
  document.getElementById('login-button').onclick = function(e) {
    e.preventDefault()
    document.getElementById('oidc_form').submit()
  }
</script>
</div>

HTML;

    //$data .= '<div><a href="' . $url . '" alt="Login With Trusona" class="trusona-no-passwords-login-button">Login with Trusona</a></div>';

    $data .= $v2html ;

    if (isset($_GET['trusona-openid-error'])) {
        $err_code = $_GET['trusona-openid-error'];
        $message  = TrusonaOpenID::$ERR_MES[$err_code];

        $data .= trusona_error_message($message);
    }

    if ($allow_wp_form) {
        $data .= '<div style="text-align: center;"><br/><script>jQuery(document).ready(function() { jQuery(\'#login\').width(\'350px\').addClass(\'login_center\'); });</script>';
        $data .= '<a href="#" style="font-size:smaller;color:#c0c0c0;" onclick="jQuery(\'form > p\').toggle();this.blur();return false;">Toggle Classic Login</a></div><br/>';
    }

    $data .= '</div>';

    return $data;
}

function trusona_error_message($message)
{
    $str = '<div style="text-align:center;margin-top:2em;color:#907878;background-color:#f1e8e5;border:1px solid darkgray;width:100%;border-radius:3px;font-weight:bolder;">';
    $str .= '<p style="line-height:1.6em;">' . $message . '</p></div><br/>';

    return $str;
}

function compute_site_hash()
{
    return sha1(parse_url(home_url(), PHP_URL_HOST));
}

?>