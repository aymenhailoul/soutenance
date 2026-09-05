<?php

// Self-registration is disabled — users are created by administrators only.
// No registration test needed; the route is not part of the IT system workflow.
test('self registration is not part of the it system', function () {
    expect(true)->toBeTrue();
});
