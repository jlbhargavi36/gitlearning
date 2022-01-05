<?php

namespace Aceturtle\CustomerAccountChanges\Controller\Account;

class CreatePostOverwritten extends \Magento\Customer\Controller\Account\CreatePost
{
    protected function checkPasswordConfirmation($password, $confirmation = null)
    {
        return true;
    }
}
