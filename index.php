<?php
//##copyright##

$iaCaptcha = $iaCore->factoryPlugin('recaptcha', iaCore::FRONT, 'captcha');

echo $iaCaptcha->getImage();