<?php

test('home redirects to admin', function () {
    $response = $this->get(route('home'));
    $response->assertRedirect('/admin');
});

test('admin panel requires authentication', function () {
    $response = $this->get('/admin');
    $response->assertRedirect('/admin/login');
});
