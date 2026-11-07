<?php

it('redirects guests to sign in', function () {
    $this->get('/')->assertRedirect(route('login'));
});
