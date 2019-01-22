<?php

namespace Laravel\Phery;

use Session;

/**
 * Extends Phery class to use Laravel session
 * @author chris
 * @todo 
 *
 */
class Phery extends \Phery
{

    /**
     * Get the current token from the $_SESSION
     *
     * @return bool
     */
    public function get_csrf_token()
    {
        if (!empty($_SESSION['phery']['csrf']))
        {
            return $_SESSION['phery']['csrf'];
        }

        return false;
    }

    /**
     * Output the meta HTML with the token.
     * This method needs to use sessions through session_start
     *
     * @param bool $check Check if the current token is valid
     * @param bool $force It will renew the current hash every call
     * @return string|bool
     */
    public function csrf($check = false, $force = false)
    {
        if ($this->config['csrf'] !== true)
        {
            return !empty($check) ? true : '';
        }

        if (session_id() == '' && $this->config['auto_session'] === true)
        {
            @session_start();
        }

        if ($check === false)
        {
            $current_token = $this->get_csrf_token();

            if (($current_token !== false && $force) || $current_token === false)
            {
                $token = sha1(uniqid(microtime(true), true));

                $_SESSION['phery'] = array(
                                'csrf' => $token
                );

                $token = base64_encode($token);
            }
            else
            {
                $token = base64_encode($_SESSION['phery']['csrf']);
            }

            return "<meta id=\"csrf-token\" name=\"csrf-token\" content=\"{$token}\" />\n";
        }
        else
        {
            if (empty($_SESSION['phery']['csrf']))
            {
                return false;
            }

            return $_SESSION['phery']['csrf'] === base64_decode($check, true);
        }
    }

    /**
     * Default shutdown handler
     *
     * @param bool $errors
     * @param bool $handled
     */
    public static function shutdown_handler($errors = false, $handled = false)
    {
        if ($handled)
        {
            self::flush();
        }

        if ($errors === true && ($error = error_get_last()) && !$handled)
        {
            self::error_handler($error["type"], $error["message"], $error["file"], $error["line"]);
        }

        if (!$handled)
        {
            self::flush();
        }

        if (session_id() != '')
        {
            session_write_close();
        }

        exit;
    }

    /**
     * Helper function to properly output the headers for a PheryResponse in case you need
     * to manually return it (like when following a redirect)
     *
     * @param string|PheryResponse $response The response or a string
     * @param bool                 $echo     Echo the response
     *
     * @return string
     */
    public static function respond($response, $echo = true)
    {
        if ($response instanceof PheryResponse)
        {
            if (!headers_sent())
            {
                if (session_id() != '') {
                    session_write_close();
                }

                header('Cache-Control: no-cache, must-revalidate', true);
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT', true);
                header('Content-Type: application/json; charset='.(strtolower(Phery::$encoding)), true);
                header('Connection: close', true);
            }
        }

        if ($response)
        {
            $response = "{$response}";
        }

        if ($echo === true)
        {
            echo $response;
        }

        return $response;
    }


}
