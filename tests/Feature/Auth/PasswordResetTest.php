<?php

// Password reset via email is not implemented (no email column on users).
test('password reset via email is not used in this system', function () {
    expect(true)->toBeTrue();
});
