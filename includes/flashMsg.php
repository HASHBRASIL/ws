<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 8/12/15
 * Time: 20:50
 */
class flashMsg
{
    /**
     * @var
     */
    protected $redirectUrl = null;

    public function addFlash($msg, $type = 'warning', $redirectUrl = null)
    {
        $_SESSION['flash_msg'][$type][] = $msg;

        if ($redirectUrl) {
            $this->redirectUrl = $redirectUrl;
        }

        return $this;
    }

    public function getFlashes($type)
    {
        $msgs = (isset($_SESSION['flash_msg'][$type])) ? $_SESSION['flash_msg'][$type] : null;

        $this->clear($type);

        return $msgs;
    }

    public function clear($type = null)
    {
        if ($type) {
            unset($_SESSION['flash_msg'][$type]);
        } else {
            unset($_SESSION['flash_msg']);
        }

        return $this;
    }

    public function info($msg, $redirectUrl = null)
    {
        return $this->addFlash($msg, 'info', $redirectUrl);
    }

    public function error($msg, $redirectUrl = null)
    {
        return $this->addFlash($msg, 'error', $redirectUrl);
    }

    public function warning($msg, $redirectUrl = null)
    {
        return $this->addFlash($msg, 'warning', $redirectUrl);
    }

    public function success($msg, $redirectUrl = null)
    {
        return $this->addFlash($msg, 'success', $redirectUrl);
    }

    public function hasFlash($type)
    {
        if (!isset($_SESSION['flash_msg'][$type])) {
            return false;
        }
        return true;
    }

    /**
     * @return $this
     */
    protected function doRedirect()
    {
        if ($this->redirectUrl) {
            header('Location: ' . $this->redirectUrl);
            exit();
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function redirectTo($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        $this->doRedirect();

        return $this;
    }
}