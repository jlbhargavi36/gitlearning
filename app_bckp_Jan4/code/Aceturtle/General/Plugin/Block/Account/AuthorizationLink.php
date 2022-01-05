<?php

namespace Aceturtle\General\Plugin\Block\Account;

class AuthorizationLink {
    
    public function aroundGetLabel(\Magento\Customer\Block\Account\AuthorizationLink $subject, callable $proceed) {
        return $subject->isLoggedIn() ? __('Log Out') : __('Log In');
    }
}