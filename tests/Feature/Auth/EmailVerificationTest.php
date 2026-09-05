<?php

// Email verification is not used in this system (no email column on users).
// Tests kept as stubs so the Auth directory is not empty.

test('email verification is not required in this system', function () {
    expect(true)->toBeTrue();
});
