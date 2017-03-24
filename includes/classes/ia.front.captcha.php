<?php

/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

class iaCaptcha extends abstractCore
{
    // Get a key from https://www.google.com/recaptcha
    protected $_publicKey = '';
    protected $_privateKey = '';

    protected $_theme;

    protected $_error;


    public function __construct()
    {
        parent::init();

        require_once dirname(__FILE__) . IA_DS . '../src/autoload.php';

        $this->_publicKey = $this->iaCore->get('recaptcha_publickey');
        $this->_privateKey = $this->iaCore->get('recaptcha_privatekey');
        $this->_theme = $this->iaCore->get('recaptcha_theme');

        if ($this->_privateKey) {
            $this->reCaptcha = new \ReCaptcha\ReCaptcha($this->_privateKey, new \ReCaptcha\RequestMethod\CurlPost());
        }
    }

    public function getImage()
    {
        if (!$this->_publicKey || !$this->_privateKey) {
            return iaLanguage::get('recaptcha_set_configuration');
        }

        $theme = '';
        if ('light' != $this->iaCore->get('recaptcha_theme')) {
            $theme = ' data-theme="' . $this->iaCore->get('recaptcha_theme') . '"';
        }

        $code = <<<CODE
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="g-recaptcha" data-sitekey="{$this->_publicKey}"{$theme}></div>
CODE;

        return $code;
    }

    public function validate()
    {
        if (iaUsers::hasIdentity()) {
            return true;
        }

        if (!empty($_POST["g-recaptcha-response"])) {
            $response = $this->reCaptcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]);

            if ($response != null && $response->isSuccess()) {
                return true;
            }
        }

        return false;
    }

    public function getPreview()
    {
        return $this->getImage();
    }
}
